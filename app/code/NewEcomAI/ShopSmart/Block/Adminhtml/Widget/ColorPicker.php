<?php

namespace NewEcomAI\ShopSmart\Block\Adminhtml\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ColorPicker extends Template implements BlockInterface
{
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $defaultColor = "#f6f6f6";
        $value = $element->getValue() ?: $defaultColor;
        $element->setData('after_element_html', '
                <input type="text"
                    style="height: 30px; width: 100px;"
                    value="' . $value . '"
                    id="' . $element->getHtmlId() . '"
                    name="' . $element->getName() . '"
                >
                <script type="text/javascript">
                require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
                    $currentElement = $("#' . $element->getHtmlId() . '");
                    $currentElement.css("backgroundColor", "'. $value .'");
                    $currentElement.ColorPicker({
                        color: "' . $value . '",
                        onChange: function (hsb, hex, rgb) {
                            $currentElement.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
                </script><style>.colorpicker {z-index: 10010}</style>');
        $element->setValue(null);
        return $element;
    }
}
