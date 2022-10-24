<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Magento\Framework\Data\Collection as AttributeCollection;

/**
 * Interface ColumnProviderInterface
 * Interface for ColumnProvider.php
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
interface ColumnProviderInterface
{
    /**
     * Returns header names for exported file
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return array
     */
    public function getHeaders(AttributeCollection $attributeCollection, array $filters): array;

    /**
     * Returns column names for Collection Select
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return array
     */
    public function getColumns(AttributeCollection $attributeCollection, array $filters): array;
}
