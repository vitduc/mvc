<?php

namespace Nsk\PartNumbersInterchange\Model\Export\Filter;

use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection;
use Nsk\PartNumbersInterchange\Model\Export\FilterProcessorInterface;

/**
 * Class VarcharFilter
 * Handle filter with varchar attribute backend type
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export\Filter
 */
class VarcharFilter implements FilterProcessorInterface
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
            $collection->addFieldToFilter($columnName, array('in' => $value));
            return;
        }
        $collection->addFieldToFilter($columnName, ['like' => '%' . $value . '%']);
    }
}
