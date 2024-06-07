<?php

namespace NewEcomAI\ShopSmart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\OrderRepositoryInterface;
use NewEcomAI\ShopSmart\Helper\Data;
use NewEcomAI\ShopSmart\Model\Log\Log;

class OrderPlace implements ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Data $helper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Data $helper
    )
    {
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $userId = $this->helper->getShopSmartUserId();
        $orderId = $order->getIncrementId();
        $orderDate = date('Y-m-d');;
        $orderItems = [];
        $orderTax = $order->getTaxAmount();
        $orderTotal = $order->getGrandTotal();

        foreach ($order->getItems() as $item) {
            $orderItems[] = [
                'id' => $item->getSku(),
                'name' => $item->getName(),
                'price' => (string)$item->getPrice()
            ];
        }
        $data = [
            "userId" => $userId,
            "source" => "Magento",
            "order" => [
                "id" => $orderId,
                "date" => $orderDate,
                "items" => $orderItems,
                "tax" => (string)$orderTax,
                "total" => (string)$orderTotal
            ]
        ];
        $decideProduct = $order->getData('discover_search_product');
        $discoverProduct = $order->getData('decide_search_product');
        if($decideProduct == 1 || $discoverProduct == 1) {
            try {
                $endpoint = "api/order/add";
                $response = $this->helper->sendApiRequest($endpoint, "POST", true, json_encode($data));
                $responseData = json_decode($response, true);
                Log::Info('Order details sent successfully: ' . json_encode($responseData));
            } catch (\Exception $e) {
                Log::Info('Error sending order details: ' . $e->getMessage());
            }
        }
    }
}
