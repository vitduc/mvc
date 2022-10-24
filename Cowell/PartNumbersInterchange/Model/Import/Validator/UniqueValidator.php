<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Nsk\PartNumbersInterchange\Model\Import\Validator;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Nsk\PartNumbersInterchange\Model\Import\Behavior\PartNumbersInterchange;

/**
 * Unique Validator
 * validate unique pair competitor - part_number
 * @author Co-well
 * @copyright Co-well All Rights Reserved
 * @package Nsk\PartNumbersInterchange\Model\Import\Validator
 */
class UniqueValidator implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @var array
     */
    protected $dataInfile = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * UniqueValidator constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ValidationResultFactory $validationResultFactory
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * validator unique pair competitor - part_number
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param array $dataDB
     * @param string $behavior
     * @return ValidationResult
     */
    public function validate(array $rowData, int $rowNumber, array $dataDB, string $behavior)
    {
        $isDataExistInFile = 0;
        $dataInDB = [];
        $errors = [];

        $competitors = $this->getCompetitorList();
        if ($rowData['competitor'] && !in_array($rowData['competitor'], $competitors)) {
            $errors[] = __('Competitor not in the list.');
            return $this->validationResultFactory->create(['errors' => $errors]);
        }

        $uniqueData = [
            'competitor' => strtolower($rowData['competitor']),
            'part_number' => strtolower($rowData['part_number']),
            'nsk_part_number' => strtolower($rowData['nsk_part_number'])
        ];
        // check unique in db
        foreach ($dataDB['nsk_part_numbers_interchange'] as $data) {
            $dataInDB[] = [
                'entity_id' => $data['entity_id'],
                'competitor' => strtolower($data['competitor']),
                'part_number' => strtolower($data['part_number']),
                'nsk_part_number' => strtolower($data['nsk_part_number'])
            ];
        }

        // case replace/delete behavior ignore check unique
        if ($behavior == PartNumbersInterchange::BEHAVIOR_ADD_UPDATE) {
            foreach ($dataInDB as $dbData) {
                // check for case update competitor/part number then skip validate
                if ($rowData['entity_id'] && $dbData['entity_id'] == $rowData['entity_id']) {
                    continue;
                } else if (array_intersect($dbData, $uniqueData) == $uniqueData) {
                    $errors[] = __('Competitor, Part Number and NSK Part number already exist in Database. If you want UPDATE this record, insert valid ENTITY_ID.');
                }
            }
        }

        // check unique in file
        foreach ($this->dataInfile as $fileData) {
            if ($fileData == $uniqueData) {
                $isDataExistInFile = 1;
            }
        }

        if ($isDataExistInFile) {
            $errors[] = __('Competitor, Part number and NSK Part Number already exist in file. If you want UPDATE this record, insert valid ENTITY_ID.');
        } else {
            $this->dataInfile[] = $uniqueData;
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }

    /**
     * @return array
     */
    public function getCompetitorList()
    {
        $listCompetitor = [];
        $competitors = $this->scopeConfig->getValue('configuration/competitors/lists');
        foreach ($competitors as $index => $value) {
            $listCompetitor[] = $value['value'];
        }

        return $listCompetitor;
    }
}
