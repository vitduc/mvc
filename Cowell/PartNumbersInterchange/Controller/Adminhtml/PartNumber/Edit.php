<?php

namespace Nsk\PartNumbersInterchange\Controller\Adminhtml\PartNumber;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Nsk\PartNumbersInterchange\Controller\Adminhtml\PartNumber as PartNumberController;

class Edit extends PartNumberController
{
    const ADMIN_RESOURCE_VIEW = 'Nsk_PartNumbersInterchange::partnumber_view';
    const ADMIN_RESOURCE_SAVE = 'Nsk_PartNumbersInterchange::partnumber_save';

    protected $resultPageFactory;
    protected $adminUser;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\Registry                $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Determines whether current user is allowed to access Distributor Action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            return $this->_authorization->isAllowed(static::ADMIN_RESOURCE_VIEW);
        } else {
            return $this->_authorization->isAllowed(static::ADMIN_RESOURCE_SAVE);
        }
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->_objectManager->create(\Nsk\PartNumbersInterchange\Model\PartNumber::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Distributor no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('nsk_part_numbers_interchange', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Part Number Interchange') : __('New Part Number Interchange'),
            $id ? __('Edit Part Number Interchange') : __('New Part Number Interchange')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Part Number'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Part Number %1', $model->getData('part_number')) : __('New Part Number Interchange'));
        return $resultPage;
    }
}
