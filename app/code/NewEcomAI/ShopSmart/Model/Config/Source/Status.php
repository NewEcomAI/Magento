<?php

namespace NewEcomAI\ShopSmart\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Check Status class
 */
class Status implements OptionSourceInterface
{

    const DISABLE = '0';
    const ENABLE = '1';


    /**
     * @var array
     */
    public static array $checkStatus = [
        self::ENABLE => 'Enable',
        self::DISABLE => 'Disable'
    ];

    /**
     * @return string[]
     */
    public static function getOptionArray(): array
    {
        return self::$checkStatus;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        $opts = [];
        foreach (self::getOptionArray() as $key => $value) {
            $opts[] = [
                'label' => __($value),
                'value' => $key,
            ];
        }
        return $opts;
    }

}
