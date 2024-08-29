<?php

namespace NewEcomAI\ShopSmart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\OrderRepositoryInterface;
use NewEcomAI\ShopSmart\Helper\SyncManagement as Data;
use NewEcomAI\ShopSmart\Model\Log\Log;
use Magento\Checkout\Model\Session as CheckoutSession;

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
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Data $helper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Data $helper,
        CheckoutSession $checkoutSession
    ) {
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Order place observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $userId = $this->helper->getShopSmartUserId();
            $orderId = $order->getIncrementId();
            $orderDate = date('Y-m-d');
            $orderItems = [];
            $orderTax = $order->getTaxAmount();
            $orderTotal = $order->getGrandTotal();
            $sessionData = $this->checkoutSession->getOrderApiData();
            $sourceArray = [];
            foreach ($order->getItems() as $item) {
                if ($item->getProductType() == 'simple') {
                    $orderItems[] = [
                        'id' => $item->getSku(),
                        'name' => $item->getName(),
                        'price' => (string)$item->getPrice()
                    ];
                    $questionId = null;
                    foreach ($sessionData as $sessionItem) {
                        if ($sessionItem['product_id'] == $item->getProductId() || $sessionItem['item_id'] == $item->getItemId()) {
                            $questionId = $sessionItem['question_id'];
                            break;
                        }
                    }
                    if ($item->getData('discover_search_product')) {
                        $sourceArray[] = [
                            'source' => 'discover',
                            'item' => [
                                'id' => $item->getProductId(),
                                'title' => $item->getName(),
                                'quantity' => $item->getQtyOrdered(),
                                'price' => $item->getPrice()
                            ],
                            'questionId' => $questionId ?? 'default-discover-question-id'
                        ];
                    }
                    if ($item->getData('decide_search_product')) {
                        $sourceArray[] = [
                            'source' => 'decide',
                            'item' => [
                                'id' => $item->getProductId(),
                                'title' => $item->getName(),
                                'quantity' => $item->getQtyOrdered(),
                                'price' => $item->getPrice()
                            ],
                            'questionId' => $questionId ?? 'default-decide-question-id'
                        ];
                    }
                }
            }

            $data = [
                "userId" => $userId,
                "order" => [
                    "id" => $orderId,
                    "date" => $orderDate,
                    "items" => $orderItems,
                    "tax" => (string)$orderTax,
                    "total" => (string)$orderTotal,
                    "source" => $sourceArray
                ]
            ];
            $endpoint = "api/order/add";
            $response = $this->helper->sendApiRequest($endpoint, "POST", true, json_encode($data));
            $responseData = json_decode($response, true);
            Log::Info('Order details sent successfully: ' . json_encode($responseData));
        } catch (\Exception $e) {
            Log::Info('Error sending order details: ' . $e->getMessage());
        }
    }
}
