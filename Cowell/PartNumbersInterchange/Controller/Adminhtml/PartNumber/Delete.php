<?php

namespace Nsk\PartNumbersInterchange\Controller\Adminhtml\PartNumber;

use Magento\Backend\App\Action\Context;
use Nsk\PartNumbersInterchange\Model\PartNumberFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    protected $_resultPageFactory;
    protected $_partNumberFactory;
    const ADMIN_RESOURCE_DELETE = 'Nsk_PartNumbersInterchange::partnumber_delete';
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        PartNumberFactory $partNumberFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_partNumberFactory = $partNumberFactory;
        parent::__construct($context);

    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(static::ADMIN_RESOURCE_DELETE);
    }
    public function execute()
    {
        $partNumberId = (int) $this->getRequest()->getParam('entity_id');

        if ($partNumberId) {
            $partNumberModel = $this->_partNumberFactory->create();
            $partNumberModel->load($partNumberId);

            // Check this news exists or not
            if (!$partNumberModel->getEntityId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
            } else {
                try {
                    // Delete news
                    $partNumberModel->delete();
                    $this->messageManager->addSuccess(__('The news has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['entity_id' => $partNumberModel->getEntityId()]);
                }
            }
        }
    }
}
