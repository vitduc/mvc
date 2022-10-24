<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Magento\Framework\Data\Collection as AttributeCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Export;


/**
 * Class ColumnProvider
 * Provide the csv columns
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
class ColumnProvider implements ColumnProviderInterface
{
    /**
     * Get header csv columns
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return array
     * @throws LocalizedException
     */
    public function getHeaders(AttributeCollection $attributeCollection, array $filters): array
    {
        // Define csv columns
        $columns = [
            AttributeCollectionProvider::INTERCHANGE_ID,
            AttributeCollectionProvider::INTERCHANGE_COMPETITOR,
            AttributeCollectionProvider::INTERCHANGE_PART_NUMBER,
            AttributeCollectionProvider::INTERCHANGE_NSK_PART_NUMBER,
            AttributeCollectionProvider::INTERCHANGE_NOTE
        ];

        if (!isset($filters[Export::FILTER_ELEMENT_SKIP])) {
            return $columns;
        }

        if (count($filters[Export::FILTER_ELEMENT_SKIP]) === count($columns)) {
            throw new LocalizedException(__('There is no data for the export.'));
        }

        // remove the skipped from columns
        $skippedAttributes = array_flip($filters[Export::FILTER_ELEMENT_SKIP]);
        foreach ($columns as $key => $value) {
            if (array_key_exists($value, $skippedAttributes) === true) {
                unset($columns[$key]);
            }
        }

        return $columns;
    }

    /**
     * Get columns
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return array
     * @throws LocalizedException
     */
    public function getColumns(AttributeCollection $attributeCollection, array $filters): array
    {
        return $this->getHeaders($attributeCollection, $filters);
    }
}
