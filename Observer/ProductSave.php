<?php

namespace NewEcomAI\ShopSmart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use NewEcomAI\ShopSmart\Helper\SyncManagement as Data;
use NewEcomAI\ShopSmart\Model\Log\Log;

class ProductSave implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     *  Apply product save operation
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $isNewProduct = $product->isObjectNew();
        $isProductUpdated = $product->hasDataChanges();
        if ($isNewProduct || $isProductUpdated) {
            $productData[] = $this->helper->getProductAttributeMapping($product->getData());
            $data = [
                "userId" => $this->helper->getShopSmartUserId(),
                "catalog" => $productData
            ];
            $endpoint = "api/catalog/update";
            $response = $this->helper->sendApiRequest($endpoint, "POST", true, json_encode($data));
            $responseData = json_decode($response, true);
            if ($responseData && isset($responseData['response']['status'])
                && $responseData['response']['status'] == 'success') {
                Log::Info($responseData['response']['status']);
            }
        }
    }
}
