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
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use NewEcomAI\ShopSmart\Model\Log;

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
     * @var Data
     */
    private Data $helperData;

    /**
     * @var ProductCollection
     */
    private ProductCollection $productCollection;

    /**
     * @param Http $http
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helperData
     * @param ProductCollection $productCollection
     */
    public function __construct(
        Http                    $http,
        Context                 $context,
        JsonFactory             $resultJsonFactory,
        Data                    $helperData,
        ProductCollection       $productCollection
    ) {
        $this->http = $http;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;
        $this->productCollection = $productCollection;
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
            try {
                $resultJson = $this->resultJsonFactory->create();
                $productCollection = $this->productCollection->addAttributeToSelect('*');

                $productCollection->setPageSize(20);
                $pages = $productCollection->getLastPageNumber();
                $productData = [];
                for ($pageNum = 1; $pageNum<=$pages; $pageNum++) {
                    $productCollection->setCurPage($pageNum);
                    foreach ($productCollection as $key => $product) {
                        $productData[] = $this->helperData->getProductAttributeMapping($product->getData());
                    }

                    $data = [
                        "userId" => $this->helperData->getShopSmartUserId(),
                        "catalog" => $productData
                    ];
                    $endpoint = "api/catalog/update";
                    $response = $this->helperData->sendApiRequest($endpoint,"POST", true, json_encode($data));
                    $responseData = json_decode($response, true);
                    if ($responseData && isset($responseData['response']['status']) && $responseData['response']['status'] == 'success') {
                        Log::Info($responseData['response']['status']);
                    }
                    $productData = [];
                    $productCollection->clear();
                }

                return $resultJson->setData(['status' => true, 'message' => "catalog Sync Successfully"]);
            } catch (\Exception $exception) {
                /** @var JsonFactory $resultJson */
                return $resultJson->setData(['status' => false, 'message' => $exception->getMessage()]);
            }
        }
    }
}
