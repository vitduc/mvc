<?php

namespace Nsk\PartNumbersInterchange\Model\Export;

use Magento\Backend\Model\Auth\Session as AdminSession;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection;
use Magento\Framework\Data\Collection as AttributeCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\ImportExport\Model\Export;

/**
 * Class PartNumbersCollectionFactory
 * Handle PartNumbers collection
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export
 */
class PartNumbersCollectionFactory implements PartNumbersCollectionFactoryInterface
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var FilterProcessorAggregator
     */
    private $filterProcessor;

    /**
     * @var ColumnProviderInterface
     */
    private $columnProvider;

    /**
     * @var AdminSession
     */
    protected $adminSession;

    /**
     * PartNumbersCollectionFactory constructor
     * Dependency injection the class
     *
     * @param ObjectManagerInterface $objectManager
     * @param FilterProcessorAggregator $filterProcessor
     * @param ColumnProviderInterface $columnProvider
     * @param AdminSession $adminSession
     */
    public function __construct(
        ObjectManagerInterface    $objectManager,
        FilterProcessorAggregator $filterProcessor,
        ColumnProviderInterface   $columnProvider,
        AdminSession              $adminSession
    )
    {
        $this->objectManager = $objectManager;
        $this->filterProcessor = $filterProcessor;
        $this->columnProvider = $columnProvider;
        $this->adminSession = $adminSession;
    }

    /**
     * Create and filter PartNumbers collections
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return Collection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): Collection
    {
        $collection = $this->getPartNumbersCollection();

        foreach ($this->retrieveFilterData($filters) as $columnName => $value) {
            $attributeDefinition = $attributeCollection->getItemById($columnName);
            if (!$attributeDefinition) {
                throw new LocalizedException(__(
                    'Given column name "%columnName" is not present in collection.',
                    ['columnName' => $columnName]
                ));
            }

            $type = $attributeDefinition->getData('backend_type');
            if (!$type) {
                throw new LocalizedException(__(
                    'There is no backend type specified for column "%columnName".',
                    ['columnName' => $columnName]
                ));
            }

            $this->filterProcessor->process($type, $collection, $columnName, $value);
        }
        return $collection;
    }

    /**
     * Retrieve filter data
     *
     * @param array $filters
     * @return array
     */
    private function retrieveFilterData(array $filters)
    {
        return array_filter(
            $filters[Export::FILTER_ELEMENT_GROUP] ?? [],
            function ($value) {
                return $value !== '';
            }
        );
    }

    /**
     * Get PartNumbers collection
     *
     * @param array $roleData
     * @return Collection
     */
    public function getPartNumbersCollection()
    {
        /** @var Collection $collection */
        $collection = $this->objectManager->create(Collection::class);
        $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['entity_id', 'competitor', 'part_number', 'nsk_part_number', 'note']);

        return $collection;
    }
}
