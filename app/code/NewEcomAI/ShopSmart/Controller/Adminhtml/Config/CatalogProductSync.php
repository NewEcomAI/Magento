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
                $productCollection->setPageSize(100);

                $productData = [];

                foreach ($productCollection as $product) {
                    $id = $product->getId();
                    $description = $product->getDescription() ?? "";
                    $name = $product->getName() ?? "";
                    $stockQty = $product->getStockQty() ?? "";
                    $categoryIds = $product->getCategoryIds() ?? [];
                    $categoryName = !empty($categoryIds) ? $this->helperData->getCategoryName($categoryIds) : "";
                    $relatedProducts = $product->getRelatedProductCollection() ?? "";
                    $status = $product->getStatus() ?? "";
                    $tags = [$stockQty, $categoryName, $relatedProducts, $status];
                    $price = $product->getPrice();
                    $productType = $product->getTypeId();
                    $category = $this->helperData->getCategoryName($categoryIds) ?? "";
                    $vendor = "magento";
                    $inventory = $product->getStockQty() ?? "";
                    $googleProductCategory = "TestCategory";
                    $ageGroup = "Test Data Adult";
                    $gender = "Test Data Women";
                    $color = "Test Data red";
                    $customProduct = "Test Data true";
                    $productData[] = $this->helperData->getProductAttributeMapping($id,$description,$name,$tags,$price,$productType,$category,$vendor,$inventory,$googleProductCategory,$ageGroup,$gender,$color,$customProduct);
                }
                $productChunks = array_chunk($productData, 20);
                foreach ($productChunks as $chunk) {
                    $data = [
                        "userId" => "662faf9377c08cce935f1aad",
                        "catalog" => $chunk
                    ];

                    $endpoint = "api/catalog/upload";
                    $headerName = "Authorization: Bearer "; // Prepare the authorisation token
                    $headerValue = $token;
                    $response = $this->helperData->sendApiRequest($endpoint,"POST", $data, $headerName, $headerValue);
                    $responseData = json_decode($response, true);
                    if ($responseData && isset($responseData['response']['status']) && $responseData['response']['status'] == 'success') {
                        echo "Catalog uploaded successfully.\n";
                    } else {
                        echo "Error uploading catalog: " . $responseData['response']['message'] . "\n";
                    }
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
