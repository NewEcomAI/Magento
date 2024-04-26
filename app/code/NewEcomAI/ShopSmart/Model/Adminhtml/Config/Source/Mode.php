<?php

namespace NewEcomAI\ShopSmart\Model\Adminhtml\Config\Source;

class Mode
{
    const STAGING = 'staging';
    const PRODUCTION = 'production';

    public function toOptionArray()
    {
        return [
            [
                'value' => Mode::STAGING,
                'label' => __('Staging')
            ],
            [
                'value' => Mode::PRODUCTION,
                'label' => __('Production')
            ],
        ];
    }
}
