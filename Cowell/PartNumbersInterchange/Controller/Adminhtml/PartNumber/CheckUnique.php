<?php

namespace Nsk\PartNumbersInterchange\Controller\Adminhtml\PartNumber;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class CheckUnique extends Action
{
    protected $_json;
    protected $_resultJsonFactory;
    protected $_collectionFactory;

    public function __construct(
        Context                                                                      $context,
        \Magento\Framework\Serialize\Serializer\Json                                 $json,
        \Magento\Framework\Controller\Result\JsonFactory                             $resultJsonFactory,
        \Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber\CollectionFactory $collectionFactory
    )
    {
        $this->_json = $json;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        $competitor = $request["competitor"] ?? '';
        $partNumber = $request["partNumber"] ?? '';
        $nskPartNumber = $request["nskPartNumber"] ?? '';
        $entityId = $request["entity_id"] ?? '';
        $isUnique = false;

        if ($competitor && $partNumber) {
            $collection = $this->_collectionFactory->create()
                ->addFieldToFilter('competitor', ['eq' => $competitor])
                ->addFieldToFilter('part_number', ['eq' => $partNumber])
                ->addFieldToFilter('nsk_part_number', ['eq' => $nskPartNumber])
                ->addFieldToFilter('entity_id', ['neq' => $entityId]);
            $isUnique = !$collection->count();
        }

        $resultJson = $this->_resultJsonFactory->create();
        $resultJson->setData(
            [
                'nsk_part_number_part_numbers_and_company_unique' => $isUnique,
            ]
        );
        return $resultJson;
    }
}
