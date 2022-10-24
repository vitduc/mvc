<?php

namespace Nsk\PartNumbersInterchange\Model\Import;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Nsk\PartNumbersInterchange\Model\Import\Validator\ValidatorInterface;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\CollectionFactory as PartNumbersCollection;
use Nsk\PartNumbersInterchange\Model\Import\Behavior\PartNumbersInterchange as BehaviorPartNumber;

/**
 * Import Part number interchange
 *
 * @package   Nsk_CompanyUserImport
 * @author    Co-well <dev@co-well.com.vn>
 * @access    public
 * @copyright Co-Well All Rights Reserved
 */
class PartNumbersInterchange extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const ENTITY_CODE = 'part_numbers_interchange';
    const TABLE = 'nsk_part_numbers_interchange';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Table name
     */
    protected $partNumbersInterchangeTable = 'nsk_part_numbers_interchange';

    protected $publisher;

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'entity_id',
        'competitor',
        'part_number',
        'nsk_part_number',
        'note'
    ];

    /**
     * @var array
     */
    private $dataDB = [];

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $connection;

    /**
     * @var PartNumbersCollection
     */
    private $partNumbersInterchangeCollectionFactory;

    /**
     * Part number constructor.
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param PartNumbersCollection $partNumbersInterchangeCollectionFactory
     * @param PublisherInterface $publisher
     * @param ValidatorInterface $validator
     */
    public function __construct(
        JsonHelper                         $jsonHelper,
        ImportHelper                       $importExportData,
        Data                               $importData,
        ResourceConnection                 $resource,
        Helper                             $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        PartNumbersCollection              $partNumbersInterchangeCollectionFactory,
        PublisherInterface                 $publisher,
        ValidatorInterface                 $validator,
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_dataSourceModel = $importData;
        $this->_resourceHelper = $resourceHelper;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->partNumbersInterchangeCollectionFactory = $partNumbersInterchangeCollectionFactory;
        $this->publisher = $publisher;
        $this->dataDB['nsk_part_numbers_interchange'] = $this->getPartNumbersInterchangeData();
        $this->validator = $validator;
    }

    /**
     * Import data rows.
     *
     * @return boolean
     * @throws Exception
     */
    protected function _importData()
    {
        $this->addEntity();
        return true;
    }

    /**
     * Import behavior getter.
     *
     * @return string
     */
    public function getBehavior()
    {
        return $this->_parameters['behavior'];
    }

    /**
     * Add and update part number interchange
     *
     * @throws Exception
     */
    private function addEntity()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $partNumbersToCreate = [];
            $partNumbersToUpdate = [];
            $partNumbersToDelete = [];
            $partNumbersData = [];

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                // prepare data to count
                if ($rowData['entity_id'] && $this->getBehavior() == BehaviorPartNumber::BEHAVIOR_ADD_UPDATE) {
                    $partNumbersToUpdate[] = 'data update';
                } else {
                    $partNumbersToCreate[] = 'data create';
                }
                $partNumbersData[] = $this->preparePartNumbersInterchangeData($rowData);
                if ($this->getPartNumbersInterchangeId($rowData) && ($this->getBehavior() == BehaviorPartNumber::BEHAVIOR_DELETE ||
                        $this->getBehavior() == BehaviorPartNumber::BEHAVIOR_REPLACE)) {
                    if ($this->getPartNumbersInterchangeId($rowData)) {
                        $partNumbersToDelete[] = $this->getPartNumbersInterchangeId($rowData);
                    }
                }
            }

            // count record created to report
            $dataCreate = ($this->getBehavior() == BehaviorPartNumber::BEHAVIOR_ADD_UPDATE) ?
                $partNumbersToCreate : $partNumbersData;
            $this->updateItemsCounterStats($dataCreate, $partNumbersToUpdate, $partNumbersToDelete);
            try {
                $this->connection->beginTransaction();

                    if ($this->getBehavior() == BehaviorPartNumber::BEHAVIOR_ADD_UPDATE) {
                        $this->addUpdatePartNumbersInterchange($partNumbersData);
                    } else if ($this->getBehavior() == BehaviorPartNumber::BEHAVIOR_REPLACE) {
                        $this->deletePartNumbersInterchange($partNumbersToDelete);
                        $this->addUpdatePartNumbersInterchange($partNumbersData);
                    } else if ($this->getBehavior() == BehaviorPartNumber::BEHAVIOR_DELETE) {
                        $this->deletePartNumbersInterchange($partNumbersToDelete);
                    }

                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Standardized part numbers structure to update into database
     *
     * @param $rowData
     * @return array
     * @throws Exception
     */
    public function preparePartNumbersInterchangeData($rowData): array
    {
        $entityRow = [];
        if ($this->getBehavior() == BehaviorPartNumber::BEHAVIOR_ADD_UPDATE) {
            $entityRow['entity_id'] = $rowData['entity_id'];
        }
        $entityRow['competitor'] = $rowData['competitor'];
        $entityRow['part_number'] = trim($rowData['part_number']);
        $entityRow['nsk_part_number'] = trim($rowData['nsk_part_number']);
        $entityRow['note'] = $rowData['note'];
        return $entityRow;
    }

    /**
     * @param $rowData
     * @return mixed|string
     */
    public function getPartNumbersInterchangeId($rowData)
    {
        $partNumbersToDelete = '';
        $dataInDB = [];
        $uniqueData = [
            'competitor' => strtolower($rowData['competitor']),
            'part_number' => strtolower($rowData['part_number']),
            'nsk_part_number' => strtolower($rowData['nsk_part_number'])
        ];

        foreach ($this->dataDB['nsk_part_numbers_interchange'] as $data) {
            $dataInDB[] = [
                'entity_id' => $data['entity_id'],
                'competitor' => strtolower($data['competitor']),
                'part_number' => strtolower($data['part_number']),
                'nsk_part_number' => strtolower($data['nsk_part_number'])
            ];
        }

        foreach ($dataInDB as $dbData) {
            if (array_intersect($dbData, $uniqueData) == $uniqueData) {
                $partNumbersToDelete = $dbData['entity_id'];
            }
        }

        return $partNumbersToDelete;
    }

    /**
     * get data from nsk_part_numbers_interchange table
     * @return array|null
     */
    private function getPartNumbersInterchangeData()
    {
        $partNumbersInterchangeCollection = $this->partNumbersInterchangeCollectionFactory->create();
        return $partNumbersInterchangeCollection->getData();
    }

    /**
     * @param array $entitiesToUpdate
     * @return $this
     */
    public function addUpdatePartNumbersInterchange(array $entitiesToUpdate)
    {
        $this->connection->insertOnDuplicate($this->partNumbersInterchangeTable, $entitiesToUpdate);
        return $this;
    }

    /**
     * @param array $entityToDelete
     * @return $this
     */
    public function deletePartNumbersInterchange(array $entityToDelete)
    {
        $this->connection->delete($this->partNumbersInterchangeTable, ['entity_id IN (?)' => $entityToDelete]);
        return $this;
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_CODE;
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $result = $this->validator->validate($rowData, $rowNum, $this->dataDB, $this->getBehavior());
        if ($result->isValid()) {
            return true;
        }

        foreach ($result->getErrors() as $error) {
            $this->addRowError($error, $rowNum);
        }

        return false;
    }

    /**
     * Update proceed items counter
     *
     * @param array $created
     * @param array $updated
     * @param array $deleted
     * @return $this
     */
    protected function updateItemsCounterStats(array $created = [], array $updated = [], array $deleted = [])
    {
        $this->countItemsCreated += ($this->getBehavior() != BehaviorPartNumber::BEHAVIOR_DELETE) ? count($created) : 0;
        $this->countItemsUpdated += count($updated);
        $this->countItemsDeleted += count($deleted);
        return $this;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }
}
