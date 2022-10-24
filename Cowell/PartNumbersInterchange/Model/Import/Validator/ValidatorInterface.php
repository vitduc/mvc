<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Nsk\PartNumbersInterchange\Model\Import\Validator;

use Magento\Framework\Validation\ValidationResult;

/**
 * Extension point for row validation (Service Provider Interface - SPI)
 *
 * @api
 */
interface ValidatorInterface
{
    /**
     * @param array $rowData
     * @param int $rowNumber
     * @param array $dataDB
     * @param string $behavior
     * @return ValidationResult
     */
    public function validate(array $rowData, int $rowNumber, array $dataDB, string $behavior);
}
