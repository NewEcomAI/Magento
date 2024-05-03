<?php

namespace NewEcomAI\ShopSmart\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Stdlib\DateTime;

class LastCatalogSyncDate extends Field
{

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->setDateFormat(DateTime::DATE_INTERNAL_FORMAT);
        $element->setTimeFormat("HH:mm:ss"); //set date and time as per your need
        return parent::render($element);
    }
}
