<?php


namespace Nsk\PartNumbersInterchange\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


/**
 * Nsk Part Numbers Interchange Search Form
 *
 */
class Form extends Template
{

    /**
     * Constructor
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }


    /**
     * Retrieve url for button clear search
     *
     * @return string
     */
    public function getUrlClearInterchageSearch()
    {
        return $this->getUrl('part_numbers/interchange/index');
    }

}
