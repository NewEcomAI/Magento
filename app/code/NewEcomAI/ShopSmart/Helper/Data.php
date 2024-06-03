<?php

namespace NewEcomAI\ShopSmart\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use NewEcomAI\ShopSmart\Model\Adminhtml\Config\Source\Mode;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Session\Generic;
use Magento\Framework\HTTP\Client\Curl;

class Data extends AbstractHelper
{
    /**
     * @var string NewCommAI authentication token
     */
    protected $token;

    const MODULE_ENABLE = 'shop_smart/general_account_configuration/enable';

    const SHOP_SMART_MODE = 'shop_smart/general_account_configuration/shop_smart_mode';
    const SHOP_SMART_USER_ID = 'shop_smart/general_account_configuration/user_id';
    const SHOP_SMART_USER_NAME = 'shop_smart/general_account_configuration/user_name';
    const SHOP_SMART_USER_PASSWORD = 'shop_smart/general_account_configuration/user_password';
    const SHOP_SMART_AB_TESTING = 'shop_smart/general_account_configuration/ab_testing';
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
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param CategoryFactory $categoryFactory
     * @param Context $context
     * @param Generic $session
     * @param Curl $httpClient
     */
    public function __construct(
        LoggerInterface             $logger,
        StoreManagerInterface       $storeManager,
        ProductRepository           $productRepository,
        ScopeConfigInterface        $scopeConfigInterface,
        CategoryFactory             $categoryFactory,
        Context                     $context,
        Generic                    $session,
        Curl                       $httpClient
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->session = $session;
        $this->httpClient = $httpClient;
        $this->categoryFactory = $categoryFactory;
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
        return $this->scopeConfig->getValue(self::SHOP_SMART_MODE,  ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartUserId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_USER_ID,  ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartUserName($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_USER_NAME,  ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShopSmartUserPassword($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_USER_PASSWORD,  ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
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
    public function getShopSmartProductAttribute($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_MAPPING, ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
    }

    /**
     * @param $storeId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getShopSmartCatalogSyncButton($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SHOP_SMART_CATALOG_SYNC_BUTTON,  ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
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
    public function getProductImageUrl($id)
    {
        $url = [];
        $product= $this->productRepository->getById($id);
        $productimages = $product->getMediaGalleryImages();
        foreach($productimages as $productimage)
        {
            $url = $productimage['url'];
        }
        return $url;
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
    public function  getProductAttributeMapping($data)
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
            $products['Description'] = $data['description'].$keyValueString;
            $products['Name'] = $valuesString;
            $categoryIds = $this->productRepository->getById($data['entity_id'])->getCategoryIds();
            $categoryName = $this->getCategoryName($categoryIds) ?? "";
            $products['Tags'] = $valuesArray;
            $products['Price'] = $data['price'] ?? "";
            $products['ProductType'] = $data['type_id'] ?? "";
            $products['Category'] = $categoryName ?? "";
            $products['Vendor'] = "magento";
            return $products;
        } catch (\Exception $e) {
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

        $response = $this->sendApiRequest($endpoint,"POST", false, $postData);
        return json_decode($response, true);

    }

    /**
     * @param $url
     * @param $method
     * @param array $data
     * @param $headerName
     * @param $headerValue
     * @return string
     */
    public function sendApiRequest($endpoint, $method, $requireOAuth , $data = [] ) {
        $mode = $this->getShopSmartMode();
        $url = '';
        if($mode === '0') {
            $url = Mode::STAGING_URL.$endpoint;
        } elseif($mode === '1') {
            $url = Mode::PRODUCTION_URL.$endpoint;
        }
        $this->httpClient->addHeader("Content-Type", "application/json");
        if($requireOAuth) {
            $outhToken = $this->getToken();
            $headers = [
                'Authorization' => 'Bearer ' . $outhToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];
            $this->httpClient->setHeaders($headers);
        }
        if(strtoupper($method) === 'POST') {
            $this->httpClient->post($url, $data);
        } elseif(strtoupper($method) === 'GET') {
            $this->httpClient->get($url);
        }
        return $this->httpClient->getBody();
    }

}






