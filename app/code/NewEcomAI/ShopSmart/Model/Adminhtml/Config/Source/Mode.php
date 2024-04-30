<?php

namespace NewEcomAI\ShopSmart\Model\Adminhtml\Config\Source;

class Mode
{

    const STAGING_URL = 'staging';
    const PRODUCTION_URL = 'production';

    public function toOptionArray()
    {
        return [
            [
                'value' => Mode::STAGING_URL,
                'label' => __('Staging')
            ],
            [
                'value' => Mode::PRODUCTION_URL,
                'label' => __('Production')
            ],
        ];
    }
}
