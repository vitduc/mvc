<?php

namespace Nsk\PartNumbersInterchange\Model;

use Magento\Framework\Model\AbstractExtensibleModel;

class PartNumber extends AbstractExtensibleModel
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'nsk_part_numbers_interchange';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Nsk\PartNumbersInterchange\Model\ResourceModel\PartNumber::class);
    }
}
