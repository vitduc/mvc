<?php

namespace Nsk\PartNumbersInterchange\Block;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Company\Model\CompanyManagement;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\SharedCatalog\Model\Repository as SharedCatalogRepo;
use Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\CollectionFactory as PartNumberCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavAttribute;
use Magento\Store\Model\StoreManagerInterface;
use Magento\SharedCatalog\Model\ResourceModel\SharedCatalog\CollectionFactory as SharedCatalogCollectionFactory;
use Magento\SharedCatalog\Api\Data\SharedCatalogInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Block for table result search Nsk Part Number Interchange
 */
class ResultList extends Template
{

    /**
     * @var PartNumberCollectionFactory
     */
    protected $partNumberCollectionFactory;

    /**
     * @var EavAttribute
     */
    protected $eavAttribute;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var CompanyManagement
     */
    protected $companyManagement;

    /**
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * @var SharedCatalogRepo
     */
    protected $sharedCatalogRepo;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var SharedCatalogCollectionFactory
     */
    protected $sharedCatalogCollectionFactory;

    private SessionManagerInterface $sessionManager;
    private CookieManagerInterface $cookieManager;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param PartNumberCollectionFactory $partNumberCollectionFactory
     * @param EavAttribute $eavAttribute
     * @param StoreManagerInterface $storeManagerInterface
     * @param CompanyManagement $companyManagement
     * @param SessionFactory $customerSession
     * @param SharedCatalogRepo $sharedCatalogRepo
     * @param HttpContext $httpContext
     * @param SharedCatalogCollectionFactory $sharedCatalogCollectionFactory
     * @param SessionManagerInterface $sessionManager
     * @param CookieManagerInterface $cookieManager
     * @param array $data
     */
    public function __construct(
        Template\Context               $context,
        PartNumberCollectionFactory    $partNumberCollectionFactory,
        EavAttribute                   $eavAttribute,
        StoreManagerInterface          $storeManagerInterface,
        CompanyManagement              $companyManagement,
        SessionFactory                 $customerSession,
        SharedCatalogRepo              $sharedCatalogRepo,
        HttpContext                    $httpContext,
        SharedCatalogCollectionFactory $sharedCatalogCollectionFactory,
        SessionManagerInterface        $sessionManager,
        CookieManagerInterface         $cookieManager,
        array                          $data = []
    )
    {
        $this->partNumberCollectionFactory = $partNumberCollectionFactory;
        $this->eavAttribute = $eavAttribute;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->companyManagement = $companyManagement;
        $this->customerSession = $customerSession;
        $this->sharedCatalogRepo = $sharedCatalogRepo;
        $this->httpContext = $httpContext;
        $this->sharedCatalogCollectionFactory = $sharedCatalogCollectionFactory;
        $this->sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }

    /**
     * Get table result list
     *
     * @return \Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\Collection|string
     */
    public function getTableList()
    {

        $requestParamPartNumber = $this->getRequest()->getParam('part_number');
        $customerGroupId = $this->getCustomerGroupId();
        if ($requestParamPartNumber) {

            $currentPage = $this->getRequest()->getParam('p') ?? 10;
            $pageSize = $this->getRequest()->getParam('limit') ?? 10;

            $currentStoreId = $this->storeManagerInterface->getStore()->getId();
            $nskProductSeriesAttributeId = $this->eavAttribute->getIdByCode('catalog_product', 'nsk_product_series');
            $productNameAttributeId = $this->eavAttribute->getIdByCode('catalog_product', 'name');
            $partNumberTableList = $this->partNumberCollectionFactory->create()
                ->getPartNumberInterchangeResultSearch($customerGroupId, $currentStoreId, $nskProductSeriesAttributeId, $productNameAttributeId);

            $searchInput = trim($requestParamPartNumber);
            $requestInputLength = strlen($searchInput);
            if ($requestInputLength) {
                if ($searchInput[0] === '*' && $searchInput[$requestInputLength - 1] !== '*') {
                    $searchLikeInput = substr_replace($searchInput, "", 0, 1);
                    $partNumberTableList->addFieldToFilter('part_number', ['like' => '%' . $searchLikeInput]);
                } elseif ($searchInput[$requestInputLength - 1] === '*' && $searchInput[0] !== '*') {
                    $searchLikeInput = substr_replace($searchInput, "", $requestInputLength - 1, 1);
                    $partNumberTableList->addFieldToFilter('part_number', ['like' => $searchLikeInput . '%']);
                } elseif ($searchInput[$requestInputLength - 1] === '*' && $searchInput[0] === '*') {
                    $removePostfixAsteriskSearchInput = substr_replace($searchInput, "", $requestInputLength - 1, 1);
                    $searchLikeInput = substr_replace($removePostfixAsteriskSearchInput, "", 0, 1);
                    if ($searchLikeInput !== '') {
                        $partNumberTableList->addFieldToFilter('part_number', ['like' => '%' . $searchLikeInput . '%']);
                    } else {
                        return '';
                    }
                } else {
                    $partNumberTableList->addFieldToFilter('part_number', $searchInput);
                }
            } else {
                return '';
            }

            $partNumberTableList->setCurPage($currentPage);
            $partNumberTableList->setPageSize($pageSize);

            return $partNumberTableList;
        }

        return '';
    }

    /**
     * Get customer group id of Customer (Logged In and Not Logged In)
     *
     * @return int
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerGroupId()
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        $customerGroupId = 0;
        if ($isLoggedIn) {
            $customer = $this->customerSession->create()->getCustomer();
            $customerId = $customer->getId();
            $company = $this->companyManagement->getByCustomerId($customerId);
            $distributorId = $this->cookieManager->getCookie('distributor_id' . $this->sessionManager->getCookiePath()) ?? null;

            if ($distributorId) {
                $sharedCatalogId = 0;
                if ($company->getExtensionAttributes() !== null &&
                    $company->getExtensionAttributes()->getDistributorCompanyAttributes() !== null) {
                    foreach ($company->getExtensionAttributes()->getDistributorCompanyAttributes() as $item) {
                        if ($item->getData('distributor_id') == $distributorId) {
                            $sharedCatalogId = $item->getData('share_catalog_id');
                            break;
                        }
                    }
                }

                if ($sharedCatalogId) {
                    try {
                        $sharedCatalog = $this->sharedCatalogRepo->get($sharedCatalogId);
                        $customerGroupId = $sharedCatalog->getCustomerGroupId();

                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        throw new LocalizedException(__("Shared Catalog doesn't exist !"));
                    }
                }
            }
        } else {
            $currentStoreId = $this->storeManagerInterface->getStore()->getId();
            $sharedCatalogCollection = $this->sharedCatalogCollectionFactory->create();
            $sharedCatalogCollection->addFieldToFilter('type', SharedCatalogInterface::TYPE_PUBLIC)
                ->addFieldToFilter('store_id', $currentStoreId);
            if ($sharedCatalogCollection->getSize() == 1) {
                $customerGroupId = $sharedCatalogCollection->getFirstItem()->getData(SharedCatalogInterface::TYPE_PUBLIC);
            }
        }

        return (int)$customerGroupId;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('nsk_part_number_interchange_result_list_pager');
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30));
            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getTableList()
            );
            return $pagerBlock->toHtml();
        }
        return '';
    }

}
