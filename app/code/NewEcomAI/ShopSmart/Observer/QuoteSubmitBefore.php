<?php

namespace NewEcomAI\ShopSmart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * Observer for product difference b/w add to cart from discover or decide
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        $discoverQuoteAttribute = $quote->getData('add_to_cart_from_discover');
        $order->setData('discover_search_product', $discoverQuoteAttribute);

        $decideQuoteAttribute = $quote->getData('add_to_cart_from_decide');
        $order->setData('decide_search_product', $decideQuoteAttribute);
    }
}
