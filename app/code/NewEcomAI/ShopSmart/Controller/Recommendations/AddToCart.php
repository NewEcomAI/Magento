<?php

namespace NewEcomAI\ShopSmart\Controller\Recommendations;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\Config;
use Magento\Checkout\Model\Session as CheckoutSession;


class AddToCart extends Action
{
    protected $resultJsonFactory;
    protected $customerSession;
    protected $cart;
    protected $productRepository;
    protected $cartRepository;
    protected $quoteFactory;
    protected $cartManagement;
    protected $storeManager;
    protected $config;
    private CheckoutSession $checkoutSession;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        CartRepositoryInterface $cartRepository,
        QuoteFactory $quoteFactory,
        CartManagementInterface $cartManagement,
        StoreManagerInterface $storeManager,
        Config $config,
        CheckoutSession $checkoutSession
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->quoteFactory = $quoteFactory;
        $this->cartManagement = $cartManagement;
        $this->storeManager = $storeManager;
        parent::__construct($context);
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $data = json_decode($this->getRequest()->getContent(), true);

        if (!isset($data['productId']) || !isset($data['colorOption']) || !isset($data['sizeOption'])) {
            return $resultJson->setData(['error' => true, 'message' => __('Invalid data.')]);
        }

        $productId = $data['productId'];
        $colorOptionLabel = $data['colorOption']['value'];
        $sizeOptionLabel = $data['sizeOption']['value'];
        $qty = isset($data['qty']) ? (int)$data['qty'] : 1;

        try {
            // Fetch the configurable product
            $configurableProduct = $this->productRepository->get($productId);

            $colorAttribute = $this->config->getAttribute('catalog_product', 'color');
            $sizeAttribute = $this->config->getAttribute('catalog_product', 'size');

            $colorOptions = $colorAttribute->getSource()->getAllOptions();
            $sizeOptions = $sizeAttribute->getSource()->getAllOptions();

            // Map option labels to option values
            $colorOptionsMap = array_column($colorOptions, 'value', 'label');
            $sizeOptionsMap = array_column($sizeOptions, 'value', 'label');

            $colorOptionValue = $colorOptionsMap[$colorOptionLabel] ?? null;
            $sizeOptionValue = $sizeOptionsMap[$sizeOptionLabel] ?? null;

            if ($colorOptionValue === null || $sizeOptionValue === null) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid product options.'));
            }

            // Prepare the super_attribute array
            $superAttribute = [
                $colorAttribute->getAttributeId() => $colorOptionValue,
                $sizeAttribute->getAttributeId() => $sizeOptionValue,
            ];

            // Add the configurable product to the cart
            $quote = $this->checkoutSession->getQuote();
            if (!$quote->getId()) {
                if ($this->customerSession->isLoggedIn()) {
                    $quote = $this->cartRepository->getActiveForCustomer($this->customerSession->getCustomerId());
                } else {
                    $quoteId = $this->cartManagement->createEmptyCart();
                    $quote = $this->quoteFactory->create()->load($quoteId);
                    $quote->setStore($this->storeManager->getStore());
                    $this->cart->setQuote($quote);
                }
            }

            $buyRequest = new \Magento\Framework\DataObject([
                'product' => $configurableProduct->getId(),
                'qty' => $qty,
                'super_attribute' => $superAttribute
            ]);

            $quote->addProduct($configurableProduct, $buyRequest);
            $quote->collectTotals()->save();
            $this->setFlag($quote->getId(),$productId);

            if (!$this->customerSession->isLoggedIn()) {
                $this->cartRepository->save($quote);
            }

            $this->cart->save(); // Ensure cart session is saved

            return $resultJson->setData(['success' => true]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $resultJson->setData(['error' => true, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $resultJson->setData(['error' => true, 'message' => __('Unable to add product to cart.')]);
        }
    }

    protected function setFlag($quoteId, $productId)
    {
        $this->checkoutSession->setNewEcomAiAddToCart(true);
        $this->checkoutSession->setNewEcomAiQuoteId($quoteId);
        $this->checkoutSession->setNewEcomAiProductId($productId);
    }
}
