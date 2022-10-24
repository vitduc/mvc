<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;
use Nsk\PartNumbersInterchange\Model\Export\Source\Competitor;

/**
 * Class AttributeCollectionProvider
 * Provide collection attribute
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
class AttributeCollectionProvider
{
    const INTERCHANGE_ID = 'entity_id';

    const INTERCHANGE_COMPETITOR = 'competitor';

    const INTERCHANGE_PART_NUMBER = 'part_number';

    const INTERCHANGE_NSK_PART_NUMBER = 'nsk_part_number';

    const INTERCHANGE_NOTE = 'note';

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * AttributeCollectionProvider constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @throws \InvalidArgumentException
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributeFactory  $attributeFactory
    )
    {
        $this->collection = $collectionFactory->create(Collection::class);
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Provide attribute for company collection
     *
     * @return Collection
     * @throws \Exception
     */
    public function get()
    {
        $attributes = [
            [
                'id' => self::INTERCHANGE_ID,
                'defaultFrontendLabel' => __('ID'),
                'attributeCode' => self::INTERCHANGE_ID,
                'backendType' => 'int'
            ],
            [
                'id' => self::INTERCHANGE_COMPETITOR,
                'defaultFrontendLabel' => __('Competitor'),
                'attributeCode' => self::INTERCHANGE_COMPETITOR,
                'backendType' => 'varchar',
                'frontendInput' => 'select',
                'sourceModel' => Competitor::class
            ],
            [
                'id' => self::INTERCHANGE_PART_NUMBER,
                'defaultFrontendLabel' => __('Part Number'),
                'attributeCode' => self::INTERCHANGE_PART_NUMBER,
                'backendType' => 'varchar'
            ],
            [
                'id' => self::INTERCHANGE_NSK_PART_NUMBER,
                'defaultFrontendLabel' => __('NSK Part Number'),
                'attributeCode' => self::INTERCHANGE_NSK_PART_NUMBER,
                'backendType' => 'varchar'
            ],
            [
                'id' => self::INTERCHANGE_NOTE,
                'defaultFrontendLabel' => __('Note'),
                'attributeCode' => self::INTERCHANGE_NOTE,
                'backendType' => 'varchar'
            ]
        ];

        if (count($this->collection) === 0) {
            foreach ($attributes as $attributeItem) {
                $attribute = $this->attributeFactory->create();
                $attribute->setId($attributeItem['id']);
                $attribute->setDefaultFrontendLabel($attributeItem['defaultFrontendLabel']);
                $attribute->setAttributeCode($attributeItem['attributeCode']);
                $attribute->setBackendType($attributeItem['backendType']);
                if (isset($attributeItem['frontendInput']) && isset($attributeItem['sourceModel'])) {
                    $attribute->setFrontendInput($attributeItem['frontendInput']);
                    $attribute->setSourceModel($attributeItem['sourceModel']);
                }
                $this->collection->addItem($attribute);
            }
        }

        return $this->collection;
    }
}
