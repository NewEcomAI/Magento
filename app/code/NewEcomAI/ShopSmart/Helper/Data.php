<?php

namespace NewEcomAI\ShopSmart\Helper;

use Magecrafts\Log\Model\Log;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Session\Generic;
use Magento\Framework\HTTP\Client\Curl;

class Data extends AbstractHelper
{
    /**
     * @var string NewCommAI authentication token
     */
    private $token;

    /**
     * @var bool Allow Retry?
     */
    private $allowRetry;

    const MODULE_ENABLE = 'shop_smart/general_account_configuration/enable';

    const SHOP_SMART_MODE = 'shop_smart/general_account_configuration/shop_smart_mode';
    const SHOP_SMART_USER_ID = 'shop_smart/general_account_configuration/user_id<';
    const SHOP_SMART_USER_NAME = 'shop_smart/general_account_configuration/user_name';
    const SHOP_SMART_USER_PASSWORD = 'shop_smart/general_account_configuration/user_password';
    const SHOP_SMART_AB_TESTING = 'shop_smart/general_account_configuration/ab_testing';
    const SHOP_SMART_CATALOG_SYNC_DATE = 'shop_smart/general_catalog_sync/catalog_sync_date';
    const SHOP_SMART_CATALOG_SYNC_BUTTON = 'shop_smart/general_catalog_sync/catalog_sync_button';
    const SHOP_SMART_MAPPING = 'shop_smart/general_product_attribute_mapping/mapping';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfigInterface;

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var RedirectInterface
     */
    private RedirectInterface $redirect;

    /**
     * @var PricingHelper
     */
    private PricingHelper $priceHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var CategoryFactory
     */
    private CategoryFactory $categoryFactory;

    /**
     * @var StockStateInterface
     */
    private StockStateInterface $stockStateInterface;


    /**
     * @var Generic
     */
    private Generic $session;

    /**
     * @var Curl
     */
    private Curl $httpClient;

    /**
     * @var mixed NewcommAI response
     */
    private $response;

    /**
     * @param LoggerInterface $logger
     * @param RedirectInterface $redirect
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param CheckoutSession $checkoutSession
     * @param ProductRepository $productRepository
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param PricingHelper $priceHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param StockStateInterface $stockStateInterface
     * @param Context $context
     * @param Generic $session
     * @param Curl $httpClient
     */
    public function __construct(
        LoggerInterface             $logger,
        RedirectInterface           $redirect,
        StoreManagerInterface       $storeManager,
        Session                     $customerSession,
        CheckoutSession             $checkoutSession,
        ProductRepository           $productRepository,
        ScopeConfigInterface        $scopeConfigInterface,
        PricingHelper               $priceHelper,
        CategoryRepositoryInterface $categoryRepository,
        CategoryFactory             $categoryFactory,
        StockStateInterface         $stockStateInterface,
        Context                     $context,
        Generic                    $session,
        Curl                       $httpClient
    ) {
        $this->logger = $logger;
        $this->redirect = $redirect;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->session = $session;
        $this->httpClient = $httpClient;
        $this->priceHelper = $priceHelper;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->stockStateInterface = $stockStateInterface;
        parent::__construct($context);
    }


    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartMode($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_MODE, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartUserId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_USER_ID, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartUserName($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_USER_NAME, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartUserPassword($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_USER_PASSWORD, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartABTesting($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_AB_TESTING, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartCatalogSyncDate($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_CATALOG_SYNC_DATE, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartProductAttribute($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_MAPPING, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartCatalogSync($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_CATALOG_SYNC_BUTTON, $storeId);
    }

    /**
     * Get Admin Configuration Values
     *
     * @param $path
     * @return mixed|void
     */
    public function getConfigValue($path)
    {
        try {
            return $this->scopeConfigInterface->getValue(
                $path,
                ScopeInterface::SCOPE_STORE,
                $this->storeManager->getStore()->getId()
            );
        } catch (\Exception $e) {
            $this->logger->critical('Config value: ' . $e->getMessage());
        }
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getProduct($sku)
    {
        if (is_int($sku)) {
            return $this->productRepository->getById($sku);
        }
        return $this->productRepository->get($sku);
    }

    /**
     * @param $sku
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getProductImageUrl($sku)
    {
        $url = [];
        $product= $this->productRepository->getById($sku);
        $productimages = $product->getMediaGalleryImages();
        foreach($productimages as $productimage)
        {
            $url = $productimage['url'];
        }
        return $url;
    }

    /**
     * @param $productId
     * @param null $websiteId
     * @return StockStateInterface
     */
    public function getStockItem($productId, $websiteId = null): StockStateInterface
    {
        return $this->stockStateInterface->getStockQty($productId, $websiteId);
    }

    /**
     * @param array $categoryIds
     * @return array|string
     */
    public function getCategoryName(array $categoryIds)
    {
        $categoryNames = [];
        foreach ($categoryIds as $key => $categoryId) {
            $category = $this->categoryFactory->create()->load($categoryId);
            $categoryNames = $category->getName();
        }
        return $categoryNames;
    }


    /**
     * @param $sku
     * @return array|void
     */
    public function getProductAttributeMapping($product)
    {
        try {
            $products = [];
            $products['Id'] = $product->getId();
            $products['Description'] = $product->getDescription();
            $products['Name'] = ucfirst($product->getData('name'));
            $products['Tags'] = [
                "red",
                "Bags",
                "medium",
                "1"
            ];
            $products['Price'] = $this->priceHelper->currency($product->getPrice(), true, false);
            $products['ProdutType'] = $product->getTypeId();
            $products['Category'] = $this->getCategoryName($product->getCategoryIds());
            $products['Vendor'] = "1";
            $products['Inventory'] = "";
            $products['GoogleProductCategory'] = "apparel & accessories > jewelry > bracelets";
            $products['AgeGroup'] = "adult";
            $products['Gender'] = "female";
            $products['Color'] = 'color';
            $products['Size'] = 'Small Size';
            $products['CustomProduct'] = "true";
            $products['ProductInfo'] = "abc";
            $products['Images'] = "http://local.magento2-2.4.6.com/media/catalog/product/m/b/mb04-black-0_alt1.jpg";

            return $products;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }




    /**
     * @return mixed|null
     */
    public function getPopUpPosition()
    {
        return $this->getConfigValue('shop_smart/general/popup');
    }

    /**
     * Get Token.
     *
     * Returns an existing token or refreshes the token and returns the new token
     *
     * @param bool $refresh
     *
     * @return string
     */
    public function getToken($refresh = true)
    {
        $accessToken = "";
        if (!$refresh) {
            if (strlen($this->token)) {
                return $this->token;
            } else if (!empty($this->session->getData('NewCommAISession'))) {
                $this->token = $this->session->getData('NewCommAISession');
                return $this->token;
            } else {
                $refresh = true;
            }
        }
        if ($refresh) {
            $accessToken = self::getAccessToken();
            if (is_string($accessToken['token'])) {
                if (!empty($accessToken['token'])) {
                    $this->session->setData('NewCommAISession', $accessToken['token']);
                }
            }
        }
        return $accessToken['token'];
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        $url = 'https://newecomenginestaging.azurewebsites.net/api/oauth/v1/token';
        $postData = json_encode([
            'username' => 'NewEcom-43987766-aadb-407e-a7d5-b3a491046560',
            'password' => '0.uq05bxxmgx',
            'userId' => '662fae5277c08cce935f1aaa'
        ]);

        $this->httpClient->addHeader('Content-Type', 'application/json');
        $this->httpClient->post($url, $postData);

        $response = $this->httpClient->getBody();

        // Handle the response here

        return json_decode($response, true);

    }
}







