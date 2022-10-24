<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Nsk\PartNumbersInterchange\Model\Import\Validator;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Nsk\PartNumbersInterchange\Model\Import\Behavior\PartNumbersInterchange;

/**
 * entity id Validator
 * @author Co-well
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Import\Validator
 */
class EntityIdValidator implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * EntityIdValidator constructor.
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory
    ) {
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * validator entity id
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param array $dataDB
     * @param string $behavior
     * @return ValidationResult
     */
    public function validate(array $rowData, int $rowNumber, array $dataDB, string $behavior)
    {
        $errors = [];
        $partNumberList = array_column($dataDB['nsk_part_numbers_interchange'], 'part_number', 'entity_id');
        if ($rowData['entity_id'] && !array_key_exists($rowData['entity_id'], $partNumberList) && $behavior == PartNumbersInterchange::BEHAVIOR_ADD_UPDATE) {
            $errors[] = __('Entity ID is not valid. If not UPDATE this record, leave entity_id blank.');
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
