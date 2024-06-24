<?php

namespace NewEcomAI\ShopSmart\Block\System\Config\Button;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use NewEcomAI\ShopSmart\Helper\Data as DataHelper;

class CatalogSyncExecution extends Field
{
    /**
     * @var DataHelper
     */
    private DataHelper $dataHelper;
    const BUTTON_TEMPLATE = 'NewEcomAI_ShopSmart::system/config/button/catalog_sync_execution.phtml';

    /**
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        context $context,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return DataHelper
     */
    public function getHelper()
    {
       return $this->dataHelper;
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render button
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }


    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $this->addData(
            [
                'button_label' => __('Catalog Sync Now'),
            ]
        );
        return $this->_toHtml();
    }
}
