<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Magento\Framework\Exception\LocalizedException;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection;
use Nsk\PartNumbersInterchange\Model\Export\FilterProcessorInterface;

/**
 * Class FilterProcessorAggregator
 * Handle the filter
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
class FilterProcessorAggregator
{
    /**
     * @var FilterProcessorInterface[]
     */
    private $handler;

    /**
     * FilterProcessorAggregator constructor
     * Dependency injection the class
     *
     * @param FilterProcessorInterface[] $handler
     * @throws LocalizedException
     */
    public function __construct(array $handler = [])
    {
        foreach ($handler as $filterProcessor) {
            if (!($filterProcessor instanceof FilterProcessorInterface)) {
                throw new LocalizedException(__(
                    'Filter handler must be instance of "%interface"',
                    ['interface' => FilterProcessorInterface::class]
                ));
            }
        }

        $this->handler = $handler;
    }

    /**
     * @param string $type
     * @param Collection $collection
     * @param string $columnName
     * @param string|array $value
     * @throws LocalizedException
     */
    public function process($type, Collection $collection, $columnName, $value)
    {
        if (!isset($this->handler[$type])) {
            throw new LocalizedException(__(
                'No filter processor for "%type" given.',
                ['type' => $type]
            ));
        }
        $this->handler[$type]->process($collection, $columnName, $value);
    }
}
