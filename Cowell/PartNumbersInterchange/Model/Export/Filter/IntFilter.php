<?php

namespace Nsk\PartNumbersInterchange\Model\Export\Filter;

use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection;
use Nsk\PartNumbersInterchange\Model\Export\FilterProcessorInterface;

/**
 * Class IntFilter
 * Handle filter with int type
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export\Filter
 */
class IntFilter implements FilterProcessorInterface
{
    /**
     * @param Collection $collection
     * @param string $columnName
     * @param array|string $value
     * @return void
     */
    public function process(Collection $collection, string $columnName, $value): void
    {
        if (is_array($value)) {
            $from = $value[0] ?? null;
            $to = $value[1] ?? null;

            if (is_numeric($from) && !empty($from)) {
                $collection->addFieldToFilter($columnName, ['from' => $from]);
            }

            if (is_numeric($to) && !empty($to)) {
                $collection->addFieldToFilter($columnName, ['to' => $to]);
            }

            return;
        }
        $collection->addFieldToFilter($columnName, ['eq' => $value]);
    }

}
