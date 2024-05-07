<?php

namespace NewEcomAI\ShopSmart\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use NewEcomAI\ShopSmart\Helper\Data;

/**
 * Check Account Validation
 */
class EcomAccountValidation extends Action
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
     * @var Data
     */
    private Data $helperData;

    /**
     * @param Http $http
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helperData
     */
    public function __construct(
        Http        $http,
        Context     $context,
        JsonFactory $resultJsonFactory,
        Data        $helperData
    ) {
        $this->http = $http;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface|void
     */
    public function execute()
    {
        if ($this->http->isAjax()) {
            $resultJson = $this->resultJsonFactory->create();
            $endpoint = "api/oauth/v1/token";
            $postData = json_encode([
                'username' => $this->helperData->getShopSmartUserName(),
                'password' => $this->helperData->getShopSmartUserPassword(),
                'userId' => $this->helperData->getShopSmartUserId()
            ]);
            $headerName = "Content-Type";
            $headerValue = "application/json";
            $response = $this->helperData->sendApiRequest($endpoint,"POST", $postData,$headerName,$headerValue);
//            return $resultJson->setData(['status' => true, 'message' => "Batch execution completed"]);
            $responseData = json_decode($response, true);
            if ($responseData && isset($responseData['token'])) {
                return $resultJson->setData(['status' => true, 'message' => "Account Validated Successfully"]);
            }
        }
    }
}
