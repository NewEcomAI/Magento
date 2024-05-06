<?php

namespace NewEcomAI\ShopSmart\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PopupLayout implements OptionSourceInterface
{
    const POPUP = 'popup';
    const POPUP_SECTION = 'section';
    const POPUP_LEFT_SIDE = 'left_side';
    const POPUP_RIGHT_SIDE = 'right_side';
    const POPUP_PRODUCT_GRID = 'product_grid';


    /**
     * @var array
     */
    public static array $popupLayout = [
        self::POPUP,
        self::POPUP_SECTION,
        self::POPUP_LEFT_SIDE,
        self::POPUP_RIGHT_SIDE,
        self::POPUP_PRODUCT_GRID
    ];

    /**
     * @return string[]
     */
    public static function getOptionArray(): array
    {
        return self::$popupLayout;
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
