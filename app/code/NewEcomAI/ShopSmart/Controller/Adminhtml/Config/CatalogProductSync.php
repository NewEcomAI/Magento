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
                $token = $this->helperData->getToken();
                $productCollection = $this->productCollection->addAttributeToSelect('*');
                $productCollection->setPageSize(3);
                // Array to store products for upload
//                $productData = [];
//                foreach ($productCollection as $product) {
//                    $products = [];
//                    $products['Id'] = $product->getId();
//                    $products['Description'] = $product->getDescription();
//                    $products['Name'] = ucfirst($product->getData('name'));
//                    $products['Tags'] = [
//                        $product->getStockQty(),
//                        "",
//                        $product->getRelatedProductCollection(),
//                        $product->getStatus()
//
//                    ];
//                    $products['Price'] = "";
//                    $products['ProductType'] = $product->getTypeId();
//                    $products['Category'] = "";
//                    $products['Vendor'] = $product->getQty();
//                    $products['Inventory'] = "";
//                    $products['Gender'] = $product->getData('gender');
//                    $products['Color'] = 'color';
//                    $products['Size'] = 'Small Size';
//                    $products['CustomProduct'] = "true";
//                    $products['ProductInfo'] = "";
//                    $products['Images'] = $product->getData('swatch_image');
////                    $productData = [
////                        "Id" => $product->getSku(),
////                        "Description" => $product->getDescription(),
////                        "Name" => $product->getName(),
////                        // Add other fields as required
////                    ];
//
//                    // Add product data to array
//                    $productData[] = $products;
//                }

                $productData = [];

                foreach ($productCollection as $product) {
                    $productData[] = $this->helperData->getProductAttributeMapping($product);
                }
                $productChunks = array_chunk($productData, 20);
                foreach ($productChunks as $chunk) {
                    $data = [
                        "userId" => "662faf9377c08cce935f1aad",
                        "catalog" => $chunk
                    ];

                    $ch = curl_init("https://newecomenginestaging.azurewebsites.net/api/catalog/upload");
                    $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Accept: application/json', $authorization
                    ]);


                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($httpCode == 200) {
                        $responseData = json_decode($response, true);
                        if ($responseData && isset($responseData['response']['status']) && $responseData['response']['status'] == 'success') {
                            echo "Catalog uploaded successfully.\n";
                        } else {
                            echo "Error uploading catalog: " . $responseData['response']['message'] . "\n";
                        }
                    } else {
                        echo "Error: HTTP $httpCode\n";
                    }

                    curl_close($ch);

                    // Add some delay between requests (optional)
                    sleep(1); // Sleep for 1 second
                }



                return $resultJson->setData(['status' => true, 'message' => "catalog Sync Successfully"]);
            } catch (\Exception $exception) {
                /** @var JsonFactory $resultJson */
                return $resultJson->setData(['status' => false, 'message' => $exception->getMessage()]);
            }
        }
    }
}
