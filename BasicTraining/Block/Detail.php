<?php

namespace Cowell\BasicTraining\Block;

use Magento\Framework\View\Element\Template;

class Detail extends Template
{
    protected $studentFactory;
    protected $request;

    public function __construct(
        Template\Context $context,
        \Cowell\BasicTraining\Model\StudentFactory $studentFactory,
        array $data = []
    ) {
        $this->studentFactory = $studentFactory;
        parent::__construct($context, $data);
        $this->pageConfig->getTitle()->set(__('Students Detail'));
    }

    public function getStudent()
    {
        $get_id = $this->getRequest()->getParam('id');
        $data = $this->studentFactory->create();
        return $data->load($get_id, 'id')->getData();
    }
}
