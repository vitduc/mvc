<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManagerInterface;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection as PartNumberCollection;

/**
 * Class PartNumbersInterchange
 * Handle PartNumbersInterchange Exporting
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
class PartNumbersInterchange extends AbstractEntity
{
    /**
     * @var AttributeCollectionProvider
     */
    private $attributeCollectionProvider;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var PartNumbersCollectionFactoryInterface
     */
    protected $partNumbersCollection;

    /**
     * @var ColumnProviderInterface
     */
    protected $columnProvider;

    /**
     * PartNumbersInterchange constructor
     * Dependency injection the class
     *
     * @param AttributeCollectionProvider $attributeCollectionProvider
     * @param ManagerInterface $messageManager
     * @param ResourceConnection $resourceConnection
     * @param PartNumbersCollectionFactoryInterface $partNumbersCollection
     * @param ColumnProviderInterface $columnProvider
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $collectionFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param array $data
     */
    public function __construct(
        AttributeCollectionProvider           $attributeCollectionProvider,
        ManagerInterface                      $messageManager,
        ResourceConnection                    $resourceConnection,
        PartNumbersCollectionFactoryInterface $partNumbersCollection,
        ColumnProviderInterface               $columnProvider,
        ScopeConfigInterface                  $scopeConfig,
        StoreManagerInterface                 $storeManager,
        ExportFactory                         $collectionFactory,
        CollectionByPagesIteratorFactory      $resourceColFactory,
        array                                 $data = []
    )
    {
        $this->attributeCollectionProvider = $attributeCollectionProvider;
        $this->messageManager = $messageManager;
        $this->resourceConnection = $resourceConnection;
        $this->partNumbersCollection = $partNumbersCollection;
        $this->columnProvider = $columnProvider;
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
    }

    /**
     * Get attribute collection
     *
     * @return Collection
     * @throws Exception
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollectionProvider->get();
    }

    /**
     * Handle export action
     *
     * @return string
     * @throws LocalizedException
     */
    public function export()
    {
        $parameters = $this->_parameters;
        unset($parameters['export_filter']['website_id']);

        /** @var PartNumberCollection $collection */
        $collection = $this->partNumbersCollection->create(
            $this->getAttributeCollection(),
            $parameters
        );

        $writer = $this->getWriter();
        $writer->setHeaderCols($this->_getHeaderColumns());

        foreach ($collection->getData() as $data) {
            $writer->writeRow($data);
        }

        return $writer->getContents();
    }

    /**
     * Get header columns
     *
     * @return array
     * @throws Exception
     */
    protected function _getHeaderColumns()
    {
        return $this->columnProvider->getHeaders($this->getAttributeCollection(), $this->_parameters);
    }

    /**
     * Export Item
     *
     * @param \Magento\Framework\Model\AbstractModel $item
     */
    public function exportItem($item)
    {
        // will not implement this method as it is legacy interface
    }

    /**
     * Get entity type code
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'part_numbers_interchange';
    }

    /**
     * Get entity collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb|void
     */
    protected function _getEntityCollection()
    {
        // will not implement this method as it is legacy interface
    }
}
