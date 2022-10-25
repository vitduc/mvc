<?php
namespace Cowell\BasicTraining\Model;

use Magento\Framework\Model\AbstractModel;
use Cowell\BasicTraining\Model\ResourceModel\Student as ResourceModel;

class Student extends AbstractModel
{
    const CACHE_TAG = 'students';

    protected $_cacheTag = 'students';

    protected $_eventPrefix = 'students';
    protected function _construct()
    {
        $this->_init('Cowell\BasicTraining\Model\ResourceModel\Student');
    }
}
