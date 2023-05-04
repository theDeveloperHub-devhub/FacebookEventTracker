<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Observer;

use DeveloperHub\FacebookEventTracker\Helper\Data;
use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Item;

class AddToCart implements ObserverInterface
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
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * AddToCart constructor.
     * @param SessionFactory $fbEventTrackerSession
     * @param Data $helper
     * @param ProductRepository $productRepository
     */
    public function __construct(
        SessionFactory $fbEventTrackerSession,
        Data $helper,
        ProductRepository $productRepository
    ) {
        $this->fbEventTrackerSession = $fbEventTrackerSession;
        $this->helper        = $helper;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @return bool
     * @throws NoSuchEntityException|LocalizedException
     */
    public function execute(Observer $observer)
    {

        $items = $observer->getItems();
        $typeConfi = Configurable::TYPE_CODE;
        if (!$this->helper->isAddToCart() || !$items) {
            return true;
        }
        $product = [
            'content_ids' => [],
            'value' => 0,
            'currency' => ""
        ];

        /** @var Item $item */
        foreach ($items as $item) {
            if ($item->getProduct()->getTypeId() == $typeConfi) {
                continue;
            }
            if ($item->getParentItem()) {
                if ($item->getParentItem()->getProductType() == $typeConfi) {
                    $product['contents'][] = [
                        'id' => $item->getSku(),
                        'name' => $item->getName(),
                        'quantity' => $item->getParentItem()->getQtyToAdd()
                    ];
                    $product['value'] += $item->getProduct()->getFinalPrice() * $item->getParentItem()->getQtyToAdd();
                } else {
                    $product['contents'][] = [
                        'id' => $item->getSku(),
                        'name' => $item->getName(),
                        'quantity' => $item->getData('qty')
                    ];
                }
            } else {
                $product['contents'][] = [
                    'id' => $this->checkBundleSku($item),
                    'name' => $item->getName(),
                    'quantity' => $item->getQtyToAdd()
                ];
                $product['value'] += $item->getProduct()->getFinalPrice() * $item->getQtyToAdd();
            }
            $product['content_ids'][] = $this->checkBundleSku($item);
        }

        $data = [
            'content_type' => 'product',
            'content_ids' => $product['content_ids'],
            'contents' => $product['contents'],
            'currency' => $this->helper->getCurrencyCode(),
            'value' => $product['value']
        ];

        $this->fbEventTrackerSession->create()->setAddToCart($data);

        return true;
    }

    /**
     * @param mixed $item
     * @return string
     * @throws NoSuchEntityException
     */
    protected function checkBundleSku($item)
    {
        $typeBundle = Type::TYPE_CODE;
        if ($item->getProductType() == $typeBundle) {
            $skuBundleProduct= $this->productRepository->getById($item->getProductId())->getSku();
            return $skuBundleProduct;
        }
        return $item->getSku();
    }
}
