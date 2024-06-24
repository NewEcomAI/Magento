<?php

namespace NewEcomAI\ShopSmart\Helper;

use \Exception;
use Psr\Log\LoggerInterface;
use NewEcomAI\ShopSmart\Model\Log\Log;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use NewEcomAI\ShopSmart\Model\Adminhtml\Config\Source\Mode;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Session\Generic;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

class Data extends AbstractHelper
{
    /**
     * Check Api limit Endpoint
     */
    const CHECK_API_LIMIT_ENDPOINT = 'api/checkLimit';
    const MODULE_ENABLE = 'shop_smart/general/account_configuration/enable';
    const SHOP_SMART_DISCOVER_WIDGET = 'shop_smart/general_newecomai_widgets/shop_smart_discover_widget';
    const SHOP_SMART_DECIDE_WIDGET = 'shop_smart/general_newecomai_widgets/shop_smart_decide_widget';
    const SHOP_SMART_MODE = 'shop_smart/general_account_configuration/shop_smart_mode';
    const SHOP_SMART_USER_ID = 'shop_smart/general_account_configuration/user_id';
    const SHOP_SMART_USER_NAME = 'shop_smart/general_account_configuration/user_name';
    const SHOP_SMART_USER_PASSWORD = 'shop_smart/general_account_configuration/user_password';
    const SHOP_SMART_AB_TESTING = 'shop_smart/general_account_configuration/ab_testing';
    const SHOP_SMART_CATALOG_SYNC_BUTTON = 'shop_smart/general_catalog_sync/catalog_sync_button';
    const SHOP_SMART_MAPPING = 'shop_smart/general/product_attribute_mapping/mapping';

    /**
     * @var string NewCommAI authentication token
     */
    protected string $token;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfigInterface;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var CategoryFactory
     */
    private CategoryFactory $categoryFactory;

    /**
     * @var Generic
     */
    private Generic $session;

    /**
     * @var Curl
     */
    private Curl $httpClient;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    protected $configDataCollectionFactory;

    /**
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param CategoryFactory $categoryFactory
     * @param Context $context
     * @param Generic $session
     * @param JsonFactory $resultJsonFactory
     * @param Curl $httpClient
     */
    public function __construct(
        LoggerInterface       $logger,
        StoreManagerInterface $storeManager,
        ProductRepository     $productRepository,
        ScopeConfigInterface  $scopeConfigInterface,
        Context               $context,
        Generic               $session,
        CategoryFactory       $categoryFactory,
        JsonFactory           $resultJsonFactory,
        CollectionFactory     $configDataCollectionFactory,
        Curl                  $httpClient
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->session = $session;
        $this->httpClient = $httpClient;
        $this->categoryFactory = $categoryFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        parent::__construct($context);
    }


    /**
     * @return false|mixed
     */
    public function isEnable()
    {
        try {
            return $this->scopeConfig->getValue(self::MODULE_ENABLE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return mixed|void
     */
    public function isDiscoverWidgetEnabled()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_DISCOVER_WIDGET, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function isDecideWidgetEnabled()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_DECIDE_WIDGET, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function getShopSmartMode()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_MODE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function getShopSmartUserId()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_USER_ID, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function getShopSmartUserName()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_USER_NAME, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;

        }
    }


    /**
     * @return false|mixed
     */
    public function getShopSmartUserPassword()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_USER_PASSWORD, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function getShopSmartABTesting()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_AB_TESTING, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function getShopSmartProductAttribute()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_MAPPING, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function getShopSmartCatalogSyncButton()
    {
        try {
            return $this->scopeConfig->getValue(self::SHOP_SMART_CATALOG_SYNC_BUTTON, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
        }
    }

    /**
     * @return string
     */
    public function getShopSmartCatalogSyncDate()
    {
        try {
            $collection = $this->configDataCollectionFactory->create()
                ->addFieldToFilter('path', self::SHOP_SMART_CATALOG_SYNC_BUTTON)
                ->setPageSize(1);

            $configData = $collection->getFirstItem();
            if ($configData->getValue()) {
                Log::info($configData->getUpdatedAt());
                return $configData->getUpdatedAt();
            } else {
                return "0000-00-00 00:00:00";
            }
        } catch (NoSuchEntityException $e) {
            Log::Error($e->getMessage());
            return false;
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
        } catch (Exception $e) {
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
     * @param $id
     * @return array|mixed|null
     */
    public function getProductImageUrl($id)
    {
        try {
            $url = [];
            $product = $this->productRepository->getById($id);
            $productimages = $product->getMediaGalleryImages();
            foreach ($productimages as $productimage) {
                $url = $productimage['url'];
            }
            return $url;
        } catch (Exception $e) {
            Log::Error($e->getMessage());
            return null;
        }
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
     * @param $data
     * @return array|void
     */
    public function getProductAttributeMapping($data)
    {
        try {
            $productAttributes = $this->getShopSmartProductAttribute();
            $attributesArray = explode(',', $productAttributes);
            $valuesString = '';
            $valuesArray = [];
            foreach ($attributesArray as $attribute) {
                if (isset($data[$attribute])) {
                    $valuesString .= $data[$attribute] . '-';
                    $valuesArray[] = $data[$attribute];
                }
            }
            $valuesString = rtrim($valuesString, '-');
            $keyValueString = '';
            foreach ($data as $key => $value) {
                if (in_array($key, $attributesArray)) {
                    $keyValueString .= "$key - $value | ";
                }
            }
            $keyValueString = rtrim($keyValueString, " | ");
            $products = [];
            $products['Id'] = $data['sku'];
            $products['Description'] = $data['description'] . $keyValueString;
            $products['Name'] = $valuesString;
            $categoryIds = $this->productRepository->getById($data['entity_id'])->getCategoryIds();
            $categoryName = $this->getCategoryName($categoryIds) ?? "";
            $products['Tags'] = $valuesArray;
            $products['Price'] = $data['price'] ?? "";
            $products['ProductType'] = $data['type_id'] ?? "";
            $products['Category'] = $categoryName ?? "";
            $products['Vendor'] = "magento";
            return $products;
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
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

    public function getToken()
    {
        $authToken = "";
        $tokenData = $this->session->getData('NewCommAISession');

        if (isset($tokenData['token']) && !empty($tokenData['token']) && isset($tokenData['expiresIn'])) {
            // Check if the token has expired
            if (time() < $tokenData['expiresAt']) {
                $authToken = $tokenData['token'];
            } else {
                // Token has expired, unset the session value
                $this->session->unsetData('NewCommAISession');
            }
        }

        if (empty($authToken)) {
            $accessTokenData = self::getAccessToken();
            if (!empty($accessTokenData['token'])) {
                $authToken = $accessTokenData['token'];
                $expiresAt = time() + $accessTokenData['expiresIn'];
                $this->session->setData('NewCommAISession', [
                    'token' => $authToken,
                    'expiresIn' => $accessTokenData['expiresIn'],
                    'expiresAt' => $expiresAt
                ]);
            }
        }

        return $authToken;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {

        $endpoint = "api/oauth/v1/token";
        $postData = json_encode([
            'username' => $this->getShopSmartUserName(),
            'password' => $this->getShopSmartUserPassword(),
            'userId' => $this->getShopSmartUserId()
        ]);

        $response = $this->sendApiRequest($endpoint, "POST", false, $postData);
        return json_decode($response, true);

    }

    /**
     * @param $endpoint
     * @param $method
     * @param $requireOAuth
     * @param $data
     * @return string
     */
    public function sendApiRequest($endpoint, $method, $requireOAuth, $data = [])
    {
        $mode = $this->getShopSmartMode();
        $url = '';
        if ($mode === Mode::STAGING_URL) {
            $url = Mode::STAGING_URL . $endpoint;
        } elseif ($mode === Mode::PRODUCTION_URL) {
            $url = Mode::PRODUCTION_URL . $endpoint;
        }
        $this->httpClient->addHeader("Content-Type", "application/json");
        if ($requireOAuth) {
            $outhToken = $this->getToken();
            $headers = [
                'Authorization' => 'Bearer ' . $outhToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];
            $this->httpClient->setHeaders($headers);
        }
        if (strtoupper($method) === 'POST') {
            $this->httpClient->post($url, $data);
        } elseif (strtoupper($method) === 'GET') {
            $this->httpClient->get($url);
        }
        return $this->httpClient->getBody();
    }

}






