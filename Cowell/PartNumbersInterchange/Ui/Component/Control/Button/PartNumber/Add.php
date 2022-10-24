<?php

namespace Nsk\PartNumbersInterchange\Ui\Component\Control\Button\PartNumber;

use Magento\Ui\Component\Control\Button;
use Magento\Framework\View\Element\Template\Context;

class Add extends Button
{
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array   $data = []
    ){
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
