<?php

namespace NewEcomAI\ShopSmart\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Product Attribute Sync
 */
class CatalogProductSync extends Action
{
    /**
     * @var Http
     */
    private Http $http;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @param Http $http
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Http        $http,
        Context     $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->http = $http;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Sync All Catalog Product Attribute
     *
     * @return ResponseInterface|Json|ResultInterface|void
     */
    public function execute()
    {
        if ($this->http->isAjax()) {
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData(['status' => true, 'message' => "catalog Sync Successfully"]);
        }
    }
}
