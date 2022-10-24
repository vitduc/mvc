<?php

namespace Nsk\PartNumbersInterchange\Ui\Component\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Backend\Helper\Data;

/**
 * Class Actions
 */
class PartNumberDataProvider extends DataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $productFactory;

    /**
     * @var Data
     */
    protected $url;

    /**
     * @var array
     */
    protected $productData = [];

    /**
     * @param CollectionFactory $productFactory
     * @param Data $url
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory     $productFactory,
        Data                  $url,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface    $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface      $request,
        FilterBuilder         $filterBuilder,
        array                 $meta = [],
        array                 $data = []
    )
    {
        $this->productFactory = $productFactory->create()
            ->addFieldToSelect(['entity_id', 'sku'])
            ->getData();
        $this->url = $url;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @return array|void
     */
    public function getData()
    {
        $this->productData = array_column($this->productFactory, 'sku', 'entity_id');
        $productDataLowCase = array_map('strtolower', $this->productData);
        $data = parent::getData();
        foreach ($data['items'] as &$item) {
            $nskPartNumber = strtolower($item['nsk_part_number']);

            if ($nskPartNumber && in_array($nskPartNumber, $productDataLowCase)) {
                $item['link'] =
                    $this->url->getUrl('catalog/product/edit',
                        ['id' => array_search($nskPartNumber, $productDataLowCase)]);
            }
        }

        return $data;
    }
}
