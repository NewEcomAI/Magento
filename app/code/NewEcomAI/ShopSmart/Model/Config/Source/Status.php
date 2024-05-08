<?php

namespace NewEcomAI\ShopSmart\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{

    const ENABLE = 'enable';
    const DISABLE = 'disable';

    /**
     * @var array
     */
    public static array $imageRecognition = [
        self::ENABLE,
        self::DISABLE,
    ];

    /**
     * @return string[]
     */
    public static function getOptionArray(): array
    {
        return self::$imageRecognition;
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

            if ($value == self::DISABLE) {
                $value = "Disable";
            } elseif ($value == self::ENABLE) {
                $value = "Enable";
            }
            $opts[] = [
                'label' => __($value),
                'value' => $key,
            ];
        }
        return $opts;
    }

}
