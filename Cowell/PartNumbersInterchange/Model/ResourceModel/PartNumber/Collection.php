<?php

namespace Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber;

class Collection extends AbstractCollection
{

    const PRODUCT_DESIGNATION_ATTRIBUTE = 'nsk_product_series';

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute            $eavAttribute,
        \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory,
        \Psr\Log\LoggerInterface                                     $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        ManagerInterface                                             $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface               $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource = null
    )
    {
        $this->_eavAttribute = $eavAttribute;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Nsk\PartNumbersInterchange\Model\PartNumber::class,
            PartNumber::class
        );
    }

    public function joinPartNumberToProductTable()
    {
        $this->getSelect()
            ->joinLeft(
                ['catalog_product_entity' => $this->getTable('catalog_product_entity')],
                'main_table.nsk_part_number = catalog_product_entity.sku',
                [
                    'sku' => 'catalog_product_entity.sku',
                    'row_id' => 'catalog_product_entity.row_id'
                ])
            ->joinLeft(
                ['catalog_product_entity_varchar' => $this->getTable('catalog_product_entity_varchar')],
                'catalog_product_entity.row_id = catalog_product_entity_varchar.row_id
                AND catalog_product_entity_varchar.attribute_id = ' . $this->getDesignationId(),
                [
                    'designation' => 'catalog_product_entity_varchar.value',
                ]
            );

        $this->addFilterToMap('designation', 'catalog_product_entity_varchar.value');
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
        return $this;
    }

    public function getDesignationId()
    {
        return $this->_eavAttribute->getIdByCode(Product::ENTITY, self::PRODUCT_DESIGNATION_ATTRIBUTE) ?? 0;
    }

    /**
     * Get data collection for page Part Number Interchange (FE Page Search)
     *
     * @param int $customerGroupId
     * @param int $currentStoreId
     * @param int $nskProductSeriesAttributeId
     * @param int $productNameAttributeId
     * @return $this
     */
    public function getPartNumberInterchangeResultSearch($customerGroupId, $currentStoreId, $nskProductSeriesAttributeId, $productNameAttributeId)
    {

        $this->getSelect()->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'main_table.nsk_part_number = product.sku',
            ['row_id', 'product_id' => 'product.entity_id']
        )->joinLeft(
            ['scpi' => $this->getTable('shared_catalog_product_item')],
            "product.sku = scpi.sku AND scpi.customer_group_id = " . $customerGroupId,
            ['sku' => 'scpi.sku']
        )->joinLeft(
            ['catalog_product' => $this->getTable('catalog_product_entity_varchar')],
            "product.row_id = catalog_product.row_id AND catalog_product.attribute_id = " . $productNameAttributeId,
            ['product_name' => 'catalog_product.value']
        )->joinLeft(
            ['url' => $this->getTable('url_rewrite')],
            'url.entity_id = product.entity_id AND url.entity_type = "' . ProductUrlRewriteGenerator::ENTITY_TYPE . '"' .
            'AND url.metadata IS NULL and url.store_id = ' . $currentStoreId,
            ['request_path']
        )->joinLeft(
            ['attribute_value' => $this->getTable('catalog_product_entity_varchar')],
            "product.row_id = attribute_value.row_id AND attribute_value.attribute_id =" . $nskProductSeriesAttributeId,
            ['product_designation' => 'attribute_value.value']
        )->joinLeft(
            ['group_product' => $this->getTable('catalog_product_entity')],
            'attribute_value.value = group_product.sku',
            ['parent_id' => 'group_product.entity_id']
        )->joinLeft(
            ['group_product_url' => $this->getTable('url_rewrite')],
            'group_product_url.entity_id = group_product.entity_id AND
                 group_product_url.entity_type = "' . ProductUrlRewriteGenerator::ENTITY_TYPE . '"' .
            'AND group_product_url.metadata IS NULL and group_product_url.store_id = ' . $currentStoreId,
            ['group_product_request_path' => 'group_product_url.request_path']
        );

        $this->getSelect()->group('main_table.entity_id');
        return $this;
    }
}
