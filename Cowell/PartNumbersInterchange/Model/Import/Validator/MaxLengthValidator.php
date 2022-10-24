<?php

declare(strict_types=1);

namespace Nsk\PartNumbersInterchange\Model\Import\Validator;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

/**
 * Max Length Validator
 * @author Co-well
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Import\Validator
 */
class MaxLengthValidator implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * length validate for fields
     */
    private $validateFields = [
        'entity_id' => 10,
        'competitor' => 40,
        'part_number' => 40,
        'nsk_part_number' => 40,
        'note' => 255
    ];

    /**
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory
    ) {
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * validator max length for fields
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
        foreach ($this->validateFields as $field => $length) {
            $data = $rowData[$field] ?? '';
            if ($data !== '' && strlen($data) > $length) {
                $inValids[] = $field;
            }
        }
        if ($inValids) {
            $errors[] = __('Field "%columns" is exceeded max length.', ['columns' => implode(",", $inValids)]);
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
