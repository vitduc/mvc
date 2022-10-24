<?php

namespace Nsk\PartNumbersInterchange\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;

abstract class PartNumber extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Nsk_PartNumbersInterchange::top_level';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Nsk'), __('Nsk'))
            ->addBreadcrumb(__('PartNumbersInterchange'), __('PartNumbersInterchange'));
        return $resultPage;
    }
}
