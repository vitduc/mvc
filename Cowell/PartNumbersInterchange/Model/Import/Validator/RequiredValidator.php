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
 * Required Validator
 * @author Co-well
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Import\Validator
 */
class RequiredValidator implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * require fields
     */

    private $requiredFields = [
        'competitor',
        'part_number',
        'nsk_part_number'
    ];

    /**
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory
    )
    {
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * validator fields is not empty
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
        $inValids = [];
        foreach ($this->requiredFields as $field) {
            $data = $rowData[$field] ?? '';
            // if delete behavior, nsk_part_number can be blank
            if ($behavior == PartNumbersInterchange::BEHAVIOR_DELETE && $field == 'nsk_part_number') {
                continue;
            } else if ($data == '' || strlen(trim($data)) == 0) {
                $inValids[] = $field;
            }
        }
        if ($inValids) {
            $errors[] = __('Please make sure field "%columns" is not empty.', ['columns' => implode(",", $inValids)]);
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
