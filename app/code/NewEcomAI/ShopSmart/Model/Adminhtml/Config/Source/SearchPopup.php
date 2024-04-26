<?php

namespace NewEcomAI\ShopSmart\Model\Adminhtml\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SearchPopup implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'popup',
                'label' => __('Popup')
            ],
            [
                'value' => 'left_popup',
                'label' => __('Left Popup')
            ],
            [
                'value' => 'right_popup',
                'label' => __('Right Popup')
            ],
            [
                'value' => 'section_popup',
                'label' => __('Section Popup')
            ],
        ];
    }
}
