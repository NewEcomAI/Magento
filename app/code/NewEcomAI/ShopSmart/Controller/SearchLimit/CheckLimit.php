<?php

namespace NewEcomAI\ShopSmart\Controller\SearchLimit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface as Response;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;

class CheckLimit extends Action
{

    /**
     * Decide Rate Question API Endpoint
     */
    protected const CHECK_LIMIT_API_ENDPOINT = "api/checkLimit";

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
    private Data $dataHelper;

    /**
     * @param Context $context
     * @param Http $http
     * @param JsonFactory $resultJsonFactory
     * @param Data $dataHelper
     */
    public function __construct(
        Context     $context,
        Http        $http,
        JsonFactory $resultJsonFactory,
        Data        $dataHelper
    ) {
        $this->http = $http;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Check Limit of widgets
     *
     * @return Response|Json|ResultInterface|void
     */
    public function execute()
    {
        $userId = $this->dataHelper->getShopSmartUserId();
        Log::Info("sssssss");
        if ($this->http->isAjax()) {
            $resultJson = $this->resultJsonFactory->create();
            $data = [
                'userId' => $userId
            ];
            $endpoint = self::CHECK_LIMIT_API_ENDPOINT;
            $response = $this->dataHelper->sendApiRequest($endpoint, "POST", true, json_encode($data));
            $responseData = json_decode($response, true);
            return $resultJson->setData(['response' => $responseData]);
        }
    }
}
