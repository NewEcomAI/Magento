<?php

namespace NewEcomAI\ShopSmart\Cron;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;

class InitialCatalogSync
{
    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * @var ProductCollection
     */
    private ProductCollection $productCollection;

    /**
     * @param Data $helperData
     * @param ProductCollection $productCollection
     */
    public function __construct(
        Data                    $helperData,
        ProductCollection       $productCollection
    ) {

        $this->helperData = $helperData;
        $this->productCollection = $productCollection;
    }
    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute()
    {
        Log::Info("InCron");
        $isCatalogSyncTriggered = $this->helperData->getShopSmartCatalogSyncButton();
        Log::Info($isCatalogSyncTriggered);

        if($isCatalogSyncTriggered){
            Log::Info("In the if condition");

            $productCollection = $this->productCollection->addAttributeToSelect('*');
            Log::Info("1");

            $productCollection->setPageSize(20);
            Log::Info("2");

            $pages = $productCollection->getLastPageNumber();
            Log::Info("3");

            $productData = [];
            for ($pageNum = 1; $pageNum<=$pages; $pageNum++) {
                Log::Info("4");

                $productCollection->setCurPage($pageNum);
                foreach ($productCollection as $key => $product) {
                    $productData[] = $this->helperData->getProductAttributeMapping($product->getData());
                }
                Log::Info("5");

                $data = [
                    "userId" => $this->helperData->getShopSmartUserId(),
                    "catalog" => $productData
                ];
                Log::Info("6");

                $endpoint = "api/catalog/update";
                Log::Info("7");

                $response = $this->helperData->sendApiRequest($endpoint,"POST", true, json_encode($data));
                Log::Info("8");

                $responseData = json_decode($response, true);
                Log::Info("9");

                if ($responseData && isset($responseData['response']['status']) && $responseData['response']['status'] == 'success') {
                    Log::Info($responseData['response']['status']);
                }
                $productData = [];
                $productCollection->clear();
                Log::Info("10");

            }
            Log::Info("Catalog Sync Completed");
        }
    }
}
