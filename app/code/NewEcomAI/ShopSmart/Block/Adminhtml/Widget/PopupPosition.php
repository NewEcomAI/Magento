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
        $popupPosition = $this->getData('shop_smart_popup_position');
        if ( $popupPosition ==  PopupLayout::POPUP_LEFT_SIDE) {
            $popupPosition = 'newcomLeftSide';
        } elseif ($popupPosition == PopupLayout::POPUP_RIGHT_SIDE) {
            $popupPosition = 'newcomRightSide';
        } elseif ($popupPosition == PopupLayout::POPUP_PRODUCT_GRID) {
            $popupPosition = 'newcomProductGrid';
        } else {
            $popupPosition = '';
        }
        return $popupPosition;
    }

    /**
     * @return array|mixed|null
     */
    public function getHeading()
    {
        return $this->getData('shop_smart_heading');
    }

    /**
     * @return array|mixed|null
     */
    public function getMessagePlaceholder()
    {
        return $this->getData('shop_smart_message_placeholder');
    }

    /**
     * @return array|mixed|null
     */
    public function getButtonText()
    {
        return $this->getData('shop_smart_button_text');
    }

    /**
     * @return array|mixed|null
     */
    public function getButtonBackgroundColor()
    {
        return $this->getData('shop_smart_button_background_color');
    }

    /**
     * @return array|mixed|null
     */
    public function getSectionBackgroundColor()
    {
        return $this->getData('shop_smart_section_button_color');
    }

    /**
     * @return array|mixed|null
     */
    public function getImageRecognition()
    {
        return $this->getData('shop_smart_image_recognition');
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryOne()
    {
        return $this->getData('shop_smart_example_query_one');
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryTwo()
    {
        return $this->getData('shop_smart_example_query_two');
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryThree()
    {
        return $this->getData('shop_smart_example_query_three');
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryFour()
    {
        return $this->getData('shop_smart_example_query_four');
    }

    /**
     * @return array|mixed|null
     */
    public function getExampleQueryFive()
    {
        return $this->getData('shop_smart_example_query_five');
    }

    /**
     * @return array|mixed|null
     */
    public function getDestinaltionStatus()
    {
        return $this->getData('shop_smart_destination_status');
    }
}
