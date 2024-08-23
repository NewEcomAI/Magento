<?php

namespace NewEcomAI\ShopSmart\Cron;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;

class InitialCatalogSync
{
    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * @var ProductCollectionFactory
     */
    private ProductCollectionFactory $productCollectionFactory;

    /**
     * @param Data $helperData
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        Data                    $helperData,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->helperData = $helperData;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute()
    {
        $isCatalogSyncTriggered = $this->helperData->getShopSmartCatalogSyncButton();
        if ($isCatalogSyncTriggered) {
            // Create the product collection using the factory
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $productCollection->addAttributeToFilter('type_id', ['in' => [Type::TYPE_SIMPLE, 'configurable']]);

            // Initialize the configurable product resource model
            $connection = $productCollection->getConnection();

            // Create a subquery to find all child product IDs of configurable products
            $subSelect = $connection->select()
                ->from(
                    ['link_table' => $productCollection->getTable('catalog_product_super_link')],
                    ['product_id']
                );

            // Exclude the simple products that are part of configurable products
            $productCollection->getSelect()->where('e.entity_id NOT IN (?)', $subSelect);

            // Load the collection
            $products = $productCollection->load();

            $products->setPageSize(20);
            $pages = $productCollection->getLastPageNumber();
            $productData = [];
            for ($pageNum = 1; $pageNum <= $pages; $pageNum++) {
                $productCollection->setCurPage($pageNum);
                foreach ($productCollection as $key => $product) {
                    $productData[] = $this->helperData->getProductAttributeMapping($product->getData());
                }

                $data = [
                    "userId" => $this->helperData->getShopSmartUserId(),
                    "catalog" => $productData
                ];
                $endpoint = "api/catalog/update";
                $response = $this->helperData
                            ->sendApiRequest($endpoint, "POST", true, json_encode($data));
                $responseData = json_decode($response, true);
                if ($responseData && isset($responseData['response']['status']) &&
                    $responseData['response']['status'] == 'success') {
                    Log::Info("Initial catalog sync cron run : " . $responseData['response']['status']);
                }
                $productData = [];
                $productCollection->clear();
            }
            Log::Info("Catalog Sync Completed");
        }
    }
}

