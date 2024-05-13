<?php

namespace NewEcomAI\ShopSmart\Block\Adminhtml\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use NewEcomAI\ShopSmart\Model\Config\Source\PopupLayout;

/**
 * Get All NewEcomAI Widget Parameters Class
 */
class PopupPosition extends Template implements BlockInterface
{
    const POPUP_CLASS = 'newcomPopup';
    const LEFT_SIDE_CLASS = 'newcomLeftSide';
    const RIGHT_SIDE_CLASS = 'newcomRightSide';
    const PRODUCT_GRID_CLASS = 'newcomProductGrid';
    const SHOP_SMART_POPUP_POSITION = 'shop_smart_popup_position';
    const SHOP_SMART_HEADING = 'shop_smart_heading';
    const SHOP_SMART_MESSAGE_PLACEHOLDER = 'shop_smart_message_placeholder';
    const SHOP_SMART_BUTTON_TEXT = 'shop_smart_button_text';
    const SHOP_SMART_BUTTON_BACKGROUND_COLOR = 'shop_smart_button_background_color';
    const SHOP_SMART_SECTION_BACKGROUND_COLOR = 'shop_smart_section_button_color';
    const SHOP_SMART_IMAGE_RECOGNITION = 'shop_smart_image_recognition';
    const SHOP_SMART_EXAMPLE_QUERY_ONE = 'shop_smart_example_query_one';
    const SHOP_SMART_EXAMPLE_QUERY_TWO = 'shop_smart_example_query_two';
    const SHOP_SMART_EXAMPLE_QUERY_THREE = 'shop_smart_example_query_three';
    const SHOP_SMART_EXAMPLE_QUERY_FOUR = 'shop_smart_example_query_four';
    const SHOP_SMART_EXAMPLE_QUERY_FIVE = 'shop_smart_example_query_five';
    const SHOP_SMART_DESTINATION_STATUS = 'shop_smart_destination_status';

    /**
     * Popup Template
     * @var string
     */
    protected $_template = "NewEcomAI_ShopSmart::widget/discover_template.phtml";

    /**
     * Get Popup Position in widget
     *
     * @return string
     */
    public function getPopupPosition()
    {
        $popupPosition = $this->getData(self::SHOP_SMART_POPUP_POSITION);
        if ($popupPosition ==  PopupLayout::POPUP_LEFT_SIDE) {
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
        return $this->getData(self::SHOP_SMART_HEADING);
    }

    /**
     * @return array|mixed|null
     */
    public function getMessagePlaceholder()
    {
        return $this->getData(self::SHOP_SMART_MESSAGE_PLACEHOLDER);
    }

    /**
     * @return array|mixed|null
     */
    public function getButtonText()
    {
        return $this->getData(self::SHOP_SMART_BUTTON_TEXT);
    }

    /**
     * @return array|mixed|null
     */
    public function getButtonBackgroundColor()
    {
        return $this->getData(self::SHOP_SMART_BUTTON_BACKGROUND_COLOR);
    }

    /**
     * @return array|mixed|null
     */
    public function getSectionBackgroundColor()
    {
        return $this->getData(self::SHOP_SMART_SECTION_BACKGROUND_COLOR);
    }

    /**
     * @return array|mixed|null
     */
    public function getImageRecognition()
    {
        return $this->getData(self::SHOP_SMART_IMAGE_RECOGNITION);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryOne()
    {
        return $this->getData(self::SHOP_SMART_EXAMPLE_QUERY_ONE);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryTwo()
    {
        return $this->getData(self::SHOP_SMART_EXAMPLE_QUERY_TWO);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryThree()
    {
        return $this->getData(self::SHOP_SMART_EXAMPLE_QUERY_THREE);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryFour()
    {
        return $this->getData(self::SHOP_SMART_EXAMPLE_QUERY_FOUR);
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryFive()
    {
        return $this->getData(self::SHOP_SMART_EXAMPLE_QUERY_FIVE);
    }

    /**
     * @return array|mixed|null
     */
    public function getDestinaltionStatus()
    {
        return $this->getData(self::SHOP_SMART_DESTINATION_STATUS);
    }
}
