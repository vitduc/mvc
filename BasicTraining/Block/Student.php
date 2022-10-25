<?php
namespace Cowell\BasicTraining\Block;

use Magento\Framework\View\Element\Template;

class Student extends Template
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
        $this->pageConfig->getTitle()->set(__('Students List'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getStudent()->getAllStudent()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15,20=>20])->setShowPerPage(false)->setCollection($this->getStudent()->getAllStudent());
            $this->setChild('pager', $pager);
            $this->getStudent()->getAllStudent()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
