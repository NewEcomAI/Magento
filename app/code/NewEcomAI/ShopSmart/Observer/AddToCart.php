<?php

namespace NewEcomAI\ShopSmart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Checkout\Model\Session as CheckoutSession;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Observes the `checkout_cart_product_add_after` event.
 */
class AddToCart implements ObserverInterface
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
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Data $helper
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Data $helper,
        CartRepositoryInterface $cartRepository

    ){
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->cartRepository = $cartRepository;
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
        $newComProduct = $this->checkoutSession->getNewEcomAiAddToCart();
        $quoteId = $this->checkoutSession->getNewEcomAiQuoteId();
        $productId = $this->checkoutSession->getNewEcomAiProductId();
        $questionId = $this->checkoutSession->getNewEcomAiQuestionId();
        $decideAddToCart = $this->checkoutSession->getNewEcomAiDecideSearchClicked();
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
        if(isset($decideAddToCart) && $decideAddToCart == true){
            $quote = $this->checkoutSession->getQuote();
            $quote->setData('add_to_cart_from_decide', 1);
            $quote->save();
            $this->cartRepository->save($quote);
            $this->checkoutSession->setNewEcomAiDecideSearchClicked(false);
        }
    }
}
