<?php

namespace NewEcomAI\ShopSmart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Checkout\Model\Session as CheckoutSession;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;

/**
 * Observes the `checkout_cart_product_add_after` event.
 */
class AddToCartObserver implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Data $helper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Data $helper

    ){
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
    }


    /**
     * Observer for checkout_cart_product_add_after.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $newComProduct = $this->checkoutSession->getNewEcomAiAddToCart(true);
        $quoteId = $this->checkoutSession->getNewEcomAiQuoteId();
        $productId = $this->checkoutSession->getNewEcomAiProductId();
        $questionId = $this->checkoutSession->getNewEcomAiQuestionId();
        if ($newComProduct && $quoteId && $productId ) {
            $data = [
                "userId" => $this->helper->getShopSmartUserId(),
                "questionId"=> $questionId,
                "productId" => $productId
            ];
            $endpoint = "api/questions/discovery/addtocart";
            $response = $this->helper->sendApiRequest($endpoint,"POST", true, json_encode($data));
            $responseData = json_decode($response, true);
            if ($responseData && isset($responseData['response']['status']) && $responseData['response']['status'] == 'success') {
                Log::Info("The product has been added to cart and synced successfully.");
            }

        }
    }
}
