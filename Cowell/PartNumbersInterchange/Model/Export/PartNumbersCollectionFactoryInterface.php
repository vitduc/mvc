<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Magento\Framework\Exception\LocalizedException;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection;
use Magento\Framework\Data\Collection as AttributeCollection;

/**
 * Interface PartNumbersCollectionFactoryInterface
 * Interface for PartNumbersInterchangeCollectionFactory.php
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
interface PartNumbersCollectionFactoryInterface
{
    /**
     * PartNumberInterchangeCollection is used to gather all the data (with filters applied) which need to be exported
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return Collection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): Collection;
}
