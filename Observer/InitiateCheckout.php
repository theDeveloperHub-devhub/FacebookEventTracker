<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Observer;

use DeveloperHub\FacebookEventTracker\Helper\Data;
use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class InitiateCheckout implements ObserverInterface
{

    /**
     * @var SessionFactory
     */
    private $fbEventTrackerSession;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * @var Data
     */
    private $fbEventTrackerHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $dataPrice;

    /**
     * InitiateCheckout constructor.
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param Data $helper
     * @param SessionFactory $fbEventTrackerSession
     * @param \Magento\Framework\Pricing\Helper\Data $dataPrice
     */
    public function __construct(
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        Data $helper,
        SessionFactory $fbEventTrackerSession,
        \Magento\Framework\Pricing\Helper\Data $dataPrice
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->fbEventTrackerHelper         = $helper;
        $this->fbEventTrackerSession = $fbEventTrackerSession;
        $this->dataPrice = $dataPrice;
    }

    /**
     * @param Observer $observer
     * @return boolean
     * @throws NoSuchEntityException|LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (!$this->fbEventTrackerHelper->isInitiateCheckout()) {
            return true;
        }
        $checkout = $this->checkoutSession->create();
        if (empty($checkout->getQuote()->getAllVisibleItems())) {
            return true;
        }

        $product = [
            'content_ids' => [],
            'contents' => [],
            'value' => "",
            'currency' => ""
        ];
        $items = $checkout->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            $product['contents'][] = [
                'id' => $item->getSku(),
                'name' => $item->getName(),
                'quantity' => $item->getQty(),
                'item_price' => $this->dataPrice->currency($item->getPrice(), false, false)
            ];
            $product['content_ids'][] = $item->getSku();
        }
        $data = [
            'content_ids' => $product['content_ids'],
            'contents' => $product['contents'],
            'content_type' => 'product',
            'value' => $checkout->getQuote()->getGrandTotal(),
            'currency' => $this->fbEventTrackerHelper->getCurrencyCode(),
        ];
        $this->fbEventTrackerSession->create()->setInitiateCheckout($data);

        return true;
    }
}
