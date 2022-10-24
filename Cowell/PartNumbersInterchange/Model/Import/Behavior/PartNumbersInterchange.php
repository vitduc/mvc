<?php

namespace Nsk\PartNumbersInterchange\Model\Import\Behavior;

/**
 * Import PartNumbersInterchange
 *
 * @package   Nsk_PartNumbersInterchange
 * @author    Co-well <dev@co-well.com.vn>
 * @access    public
 * @copyright Co-Well All Rights Reserved
 */
class PartNumbersInterchange extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    const BEHAVIOR_ADD_UPDATE = 'add_update';
    const BEHAVIOR_REPLACE = 'replace';
    const BEHAVIOR_DELETE = 'delete';

    /**
     * Get array of possible values
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::BEHAVIOR_ADD_UPDATE => __('Add/Update'),
            self::BEHAVIOR_REPLACE => __('Replace'),
            self::BEHAVIOR_DELETE => __('Delete')
        ];
    }

    /**
     * Get current behaviour group code
     *
     * @return string
     */
    public function getCode()
    {
        return 'partNumbersInterchange';
    }
}
