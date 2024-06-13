<?php

namespace NewEcomAI\ShopSmart\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use NewEcomAI\ShopSmart\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableResource;


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
     * @var WriterInterface
     */
    private WriterInterface $writer;

    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * @var ProductCollection
     */
    private ProductCollection $productCollection;
    private ConfigurableResource $configurableResource;

    /**
     * @param Http $http
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helperData
     * @param ProductCollection $productCollection
     * @param WriterInterface $writer
     */
    public function __construct(
        Http                    $http,
        Context                 $context,
        JsonFactory             $resultJsonFactory,
        Data                    $helperData,
        ProductCollection       $productCollection,
        WriterInterface         $writer,
        ConfigurableResource    $configurableResource
    ) {
        $this->http = $http;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;
        $this->productCollection = $productCollection;
        $this->writer = $writer;
        $this->configurableResource = $configurableResource;
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
                $catalogSynced = $this->getRequest()->getParam("buttonClicked");
                if($catalogSynced == true){
                    $this->writer->save("shop_smart/general_catalog_sync/catalog_sync_button", "1");
                }
                return $resultJson->setData(['status' => true, 'message' => "Catalog Syncing has been started in the background."]);
            } catch (\Exception $exception) {
                /** @var JsonFactory $resultJson */
                return $resultJson->setData(['status' => false, 'message' => $exception->getMessage()]);
            }
        }
    }
}
