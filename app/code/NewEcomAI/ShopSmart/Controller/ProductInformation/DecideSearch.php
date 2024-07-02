<?php

namespace NewEcomAI\ShopSmart\Controller\ProductInformation;

use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\Catalog\Model\Product\Url as ProductUrl;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;
use Magento\Checkout\Model\Session as CheckoutSession;

class DecideSearch extends Action
{
    /**
     * Decide Product Information API Endpoint
     */
    protected const DECIDE_API_ENDPOINT = "api/product/decide";

    /**
     * @var Http
     */
    private Http $http;

    /**
     * @var Data
     */
    private Data $dataHelper;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var AttributeRepository
     */
    protected AttributeRepository $attributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var StockItemRepository
     */
    protected StockItemRepository $stockItemRepository;

    /**
     * @var Configurable
     */
    private Configurable $configurable;

    /**
     * @var ProductUrl
     */
    private ProductUrl $productUrl;

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @param Context $context
     * @param Http $http
     * @param JsonFactory $resultJsonFactory
     * @param Data $dataHelper
     * @param ProductRepository $productRepository
     * @param AttributeRepository $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param StockItemRepository $stockItemRepository
     * @param Configurable $configurable
     * @param ProductUrl $productUrl
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context                             $context,
        Http                                $http,
        JsonFactory                         $resultJsonFactory,
        Data                                $dataHelper,
        ProductRepository                   $productRepository,
        AttributeRepository                 $attributeRepository,
        SearchCriteriaBuilder               $searchCriteriaBuilder,
        StoreManagerInterface               $storeManager,
        StockItemRepository                 $stockItemRepository,
        Configurable                        $configurable,
        ProductUrl                          $productUrl,
        CheckoutSession                     $checkoutSession
    ) {
        $this->http = $http;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->stockItemRepository = $stockItemRepository;
        $this->configurable = $configurable;
        $this->productUrl = $productUrl;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Decide search controller
     *
     * @return ResponseInterface|Json|ResultInterface|void
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $searchKey = $params['searchKey'];
        $questionId = $params['questionId'];
        $currentProductTitle = $params['currentProductTitle'];
        $currentProductDescription = $params['currentProductDescription'];
        $userId = $this->dataHelper->getShopSmartUserId();
        $this->checkoutSession->setNewEcomAiDecideSearchClicked(true);
        if ($this->http->isAjax()) {
            $resultJson = $this->resultJsonFactory->create();
            if (empty($questionId)) {
                $data = [
                    'userId' => $userId,
                    'listQuestions' => [$searchKey],
                    'currentProduct' => [
                        'title' => $currentProductTitle,
                        'body_html' => $currentProductDescription
                    ]
                ];
            } else {
                $data = [
                    'userId' => $userId,
                    "questionId" => $questionId,
                    'listQuestions' => [$searchKey],
                    'currentProduct' => [
                        'title' => $currentProductTitle,
                        'body_html' => $currentProductDescription
                    ]
                ];

            }
            $endpoint = self::DECIDE_API_ENDPOINT;
            $response = $this->dataHelper->sendApiRequest($endpoint, "POST", true, json_encode($data));
            $responseData = json_decode($response, true);

            return $resultJson->setData(['response' => $responseData]);

        }
    }
}
