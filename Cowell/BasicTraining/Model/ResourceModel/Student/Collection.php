<?php
namespace Cowell\BasicTraining\Model\ResourceModel\Student;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Cowell\BasicTraining\Model\Student', 'Cowell\BasicTraining\Model\ResourceModel\Student');
    }


}
