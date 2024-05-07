<?php

namespace NewEcomAI\ShopSmart\Model\Adminhtml\Config\Source;

class Mode
{

    const STAGING_URL = 'https://newecomenginestaging.azurewebsites.net/';
    const PRODUCTION_URL = 'production';

    /**
     * @var array
     */
    public static array $modeUrl = [
        self::STAGING_URL,
        self::PRODUCTION_URL
    ];

    /**
     * @return string[]
     */
    public static function getOptionArray(): array
    {
        return self::$modeUrl;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        $opts = [];
        $label = '';
        foreach (self::getOptionArray() as $key => $value) {
            if ($value == self::STAGING_URL) {
                $label = "Staging";
            } elseif ($value == self::PRODUCTION_URL) {
                $label = "Production";
            }
            $opts[] = [
                'label' => __($label),
                'value' => $key,
            ];
        }
        return $opts;
    }








}
