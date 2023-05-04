<?php
declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Observer;

use DeveloperHub\FacebookEventTracker\Helper\Data;
use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class WishlistAddProduct implements ObserverInterface
{

    /**
     * @var SessionFactory
     */
    private $fbEventTrackerSession;

    /**
     * @var Data
     */
    private $helper;

    /**
     * WishlistAddProduct constructor.
     * @param SessionFactory $fbEventTrackerSession
     * @param Data $helper
     */
    public function __construct(
        SessionFactory $fbEventTrackerSession,
        Data $helper
    ) {
        $this->fbEventTrackerSession = $fbEventTrackerSession;
        $this->helper        = $helper;
    }

    /**
     * @param Observer $observer
     * @return boolean
     * @throws NoSuchEntityException|LocalizedException
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getItem()->getProduct();
        /** @var Product $product */
        if (!$this->helper->isAddToWishList() || !$product) {
            return true;
        }

        $data = [
            'content_type' => 'product',
            'content_ids' => $product->getSku(),
            'content_name' => $product->getName(),
            'currency' => $this->helper->getCurrencyCode()
        ];

        $this->fbEventTrackerSession->create()->setAddToWishlist($data);

        return true;
    }
}
