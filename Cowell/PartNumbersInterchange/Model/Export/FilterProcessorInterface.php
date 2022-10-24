<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection;

/**
 * Interface FilterProcessorInterface
 * Interface for handle filter
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
interface FilterProcessorInterface
{
    /**
     * Filter Processor Interface is used as an Extension Point for each Attribute Data Type (Backend Type)
     * to process filtering applied from Export Grid UI
     * to all attributes of Entity being exported
     *
     * @param Collection $collection
     * @param string $columnName
     * @param array|string $value
     * @return void
     */
    public function process(Collection $collection, string $columnName, $value): void;
}
