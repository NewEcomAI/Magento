<?php

namespace NewEcomAI\ShopSmart\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ProductGrid implements OptionSourceInterface
{
    const ONE_PRODUCTS_ROW = 'one_products_row';
    const TWO_PRODUCTS_ROW = 'two_products_row';
    const THREE_PRODUCTS_ROW = 'three_products_row';
    const FOUR_PRODUCTS_ROW = 'four_products_row';
    const FIVE_PRODUCTS_ROW = 'five_products_row';
    const SIX_PRODUCTS_ROW = 'six_products_row';

    /**
     * @var array
     */
    public static array $layoutProductGrid = [
        self::ONE_PRODUCTS_ROW,
        self::TWO_PRODUCTS_ROW,
        self::THREE_PRODUCTS_ROW,
        self::FOUR_PRODUCTS_ROW,
        self::FIVE_PRODUCTS_ROW,
        self::SIX_PRODUCTS_ROW
    ];

    /**
     * @return string[]
     */
    public static function getOptionArray(): array
    {
        return self::$layoutProductGrid;
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

            if ($value == self::ONE_PRODUCTS_ROW) {
                $value = "1 Product/Row";
            } elseif ($value == self::TWO_PRODUCTS_ROW) {
                $value = "2 Product/Row";
            } elseif ($value == self::THREE_PRODUCTS_ROW) {
                $value = "3 Product/Row";
            } elseif ($value == self::FOUR_PRODUCTS_ROW) {
                $value = "4 Product/Row";
            } elseif ($value == self::FIVE_PRODUCTS_ROW) {
                $value = "5 Product/Row";
            }
            elseif ($value == self::SIX_PRODUCTS_ROW) {
                $value = "6 Product/Row";
            }
            $opts[] = [
                'label' => __($value),
                'value' => $key,
            ];
        }
        return $opts;
    }
}
