<?php

namespace NewEcomAI\ShopSmart\Block\Adminhtml\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Config\Source\PopupLayout;
use Magento\Catalog\Block\Product\View as ProductViewBlock;

class DecidePopup extends Template implements BlockInterface
{
    /**
     * Decide Template
     * @var string
     */
    protected $_template = "NewEcomAI_ShopSmart::widget/decide_template.phtml";
    const DECIDE_SEARCH_CONTROLLER_PATH = "newecomai/productinformation/decidesearch";
    const DECIDE_RATE_QUESTION_CONTROLLER_PATH = "newecomai/productinformation/ratequestion";

    const POPUP_CLASS = 'newcomPopup';
    const LEFT_SIDE_CLASS = 'newcomLeftSide';
    const RIGHT_SIDE_CLASS = 'newcomRightSide';
    const PRODUCT_GRID_CLASS = 'newcomProductGrid';
    const SHOP_SMART_DECIDE_POPUP_POSITION = 'shop_smart_decide_popup_position';
    const SHOP_SMART_DECIDE_LAYOUT_PRODUCT_GRID = 'shop_smart_decide_layout_product_grid';
    const SHOP_SMART_DECIDE_HEADING = 'shop_smart_decide_heading';
    const SHOP_SMART_DECIDE_MESSAGE_PLACEHOLDER = 'shop_smart_decide_message_placeholder';
    const SHOP_SMART_DECIDE_BUTTON_TEXT = 'shop_smart_decide_button_text';
    const SHOP_SMART_DECIDE_BUTTON_BACKGROUND_COLOR = 'shop_smart_decide_button_background_color';
    const SHOP_SMART_DECIDE_SECTION_BACKGROUND_COLOR = 'shop_smart_decide_section_button_color';
    const SHOP_SMART_DECIDE_IMAGE_RECOGNITION = 'shop_smart_decide_image_recognition';
    const SHOP_SMART_DECIDE_LAYOUT_TEXT = 'shop_smart_decide_layout_text';
    const SHOP_SMART_DECIDE_LAYOUT_DESCRIPTION_TEXT = 'shop_smart_decide_layout_description_text';
    const SHOP_SMART_DECIDE_EXAMPLE_QUERY_ONE = 'shop_smart_decide_example_query_one';
    const SHOP_SMART_DECIDE_EXAMPLE_QUERY_TWO = 'shop_smart_decide_example_query_two';
    const SHOP_SMART_DECIDE_EXAMPLE_QUERY_THREE = 'shop_smart_decide_example_query_three';
    const SHOP_SMART_DECIDE_EXAMPLE_QUERY_FOUR = 'shop_smart_decide_example_query_four';
    const SHOP_SMART_DECIDE_EXAMPLE_QUERY_FIVE = 'shop_smart_decide_example_query_five';
    const SHOP_SMART_DECIDE_DESTINATION_STATUS = 'shop_smart_decide_destination_status';
    const SHOP_SMART_DECIDE_CUSTOM_CSS = 'shop_smart_decide_custom_css';

    /**
     * @var Data
     */
    protected Data $helperData;

    protected ProductViewBlock $productViewBlock;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param ProductViewBlock $productViewBlock
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data             $helperData,
        ProductViewBlock $productViewBlock,
        array            $data = []
    ) {
        $this->productViewBlock = $productViewBlock;
        $this->helperData = $helperData;
        $this->data = $data;
        parent::__construct($context);
    }

    public function getCurrentProduct()
    {
        return $this->productViewBlock->getProduct();
    }

    /**
     * @return Data
     */
    public function getDatahelper()
    {
        return $this->helperData;
    }

    /**
     * @return array|mixed|null
     */
    public function getProductGrid()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_LAYOUT_PRODUCT_GRID);
    }

    /**
     * Get Popup Position in widget
     *
     * @return string
     */
    public function getPopupPosition()
    {
        $popupPosition = $this->getData(self::SHOP_SMART_DECIDE_POPUP_POSITION);
        if ($popupPosition == PopupLayout::POPUP_LEFT_SIDE) {
            $popupPosition = self::LEFT_SIDE_CLASS;
        } elseif ($popupPosition == PopupLayout::POPUP_RIGHT_SIDE) {
            $popupPosition = self::RIGHT_SIDE_CLASS;
        } elseif ($popupPosition == PopupLayout::POPUP_PRODUCT_GRID) {
            $popupPosition = self::PRODUCT_GRID_CLASS;
        } else {
            $popupPosition = self::POPUP_CLASS;
        }
        return $popupPosition;
    }

    /**
     * @return array|mixed|null
     */
    public function getHeading()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_HEADING);
    }

    /**
     * @return string
     */
    public function getDecideSearchUrl()
    {
        return $this->getUrl(self:: DECIDE_SEARCH_CONTROLLER_PATH);
    }

    /**
     * @return string
     */
    public function getDecideSearchQuestionRateUrl()
    {
        return $this->getUrl(self::DECIDE_RATE_QUESTION_CONTROLLER_PATH);
    }

    /**
     * @return array|mixed|null
     */
    public function getMessagePlaceholder()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_MESSAGE_PLACEHOLDER);
    }

    /**
     * @return array|mixed|null
     */
    public function getButtonText()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_BUTTON_TEXT);
    }

    /**
     * @return array|mixed|null
     */
    public function getButtonBackgroundColor()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_BUTTON_BACKGROUND_COLOR);
    }

    /**
     * @return array|mixed|null
     */
    public function getSectionBackgroundColor()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_SECTION_BACKGROUND_COLOR);
    }

    /**
     * @return array|mixed|null
     */
    public function getImageRecognition()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_IMAGE_RECOGNITION);
    }

    /**
     * @return array|mixed|null
     */
    public function getLayoutText()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_LAYOUT_TEXT);
    }

    /**
     * @return array|mixed|null
     */
    public function getLayoutDescriptionText()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_LAYOUT_DESCRIPTION_TEXT);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryOne()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_EXAMPLE_QUERY_ONE);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryTwo()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_EXAMPLE_QUERY_TWO);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryThree()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_EXAMPLE_QUERY_THREE);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryFour()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_EXAMPLE_QUERY_FOUR);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryFive()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_EXAMPLE_QUERY_FIVE);
    }

    /**
     * @return array|mixed|null
     */
    public function getDestinaltionStatus()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_DESTINATION_STATUS);
    }

    /**
     * @return array|mixed|null
     */
    public function getCustomCss()
    {
        return $this->getData(self::SHOP_SMART_DECIDE_CUSTOM_CSS);
    }

}
