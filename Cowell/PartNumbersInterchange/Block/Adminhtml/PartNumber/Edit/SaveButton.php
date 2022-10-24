<?php

namespace Nsk\PartNumbersInterchange\Block\Adminhtml\PartNumber\Edit;

class SaveButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{

    protected $dataPersistor;

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
            'aclResource' => 'Nsk_PartNumbersInterchange::partnumber_save'
        ];
    }
}
