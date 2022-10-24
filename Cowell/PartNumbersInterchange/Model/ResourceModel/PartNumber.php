<?php

namespace Nsk\PartNumbersInterchange\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class PartNumber extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('nsk_part_numbers_interchange', 'entity_id');
    }
}
