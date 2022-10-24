<?php

namespace Nsk\PartNumbersInterchange\Block\Adminhtml\PartNumber\Edit;

class DeleteButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{

    /**
     * Get button data.
     *
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->context->getRequest()->getParam('entity_id')) {
            return [];
        }
        $data = [
            'label' => __('Delete'),
            'class' => 'delete',
            'id' => 'partnumber-edit-delete-button',
            'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this part number?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {data: {}})',
            'sort_order' => 20,
            'aclResource' => 'Nsk_PartNumbersInterchange::partnumber_delete'
        ];
        return $data;
    }

    /**
     * Get delete url.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        $partNumberId = $this->context->getRequest()->getParam('entity_id');
        return $this->getUrl('*/*/delete', ['entity_id' => $partNumberId]);
    }
}
