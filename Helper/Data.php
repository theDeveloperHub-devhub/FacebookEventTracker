<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Config;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $taxConfig;

    /**
     * @var Json
     */
    private $jsonEncoder;

    /**
     * Tax display flag
     *
     * @var null|int
     */
    private $taxDisplayFlag = null;

    /**
     * Tax catalog flag
     *
     * @var null|int
     */
    private $taxCatalogFlag = null;

    /**
     * Store object
     *
     * @var null|Store
     */
    private $store = null;

    /**
     * Store ID
     *
     * @var null|int
     */
    private $storeId = null;

    /**
     * Base currency code
     *
     * @var null|string
     */
    private $baseCurrencyCode = null;

    /**
     * Current currency code
     *
     * @var null|string
     */
    private $currentCurrencyCode = null;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $catalogHelper;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Config $taxConfig
     * @param Json $jsonEncoder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Config $taxConfig,
        Json $jsonEncoder,
        \Magento\Catalog\Helper\Data $catalogHelper
    ) {
        $this->scopeConfig          = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        $this->taxConfig = $taxConfig;
        $this->jsonEncoder = $jsonEncoder;
        $this->catalogHelper = $catalogHelper;

        parent::__construct($context);
    }

    /**
     * @param array $data
     * @return false|string
     */
    public function serializes($data)
    {
        $result = $this->jsonEncoder->serialize($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }
        return $result;
    }

    /**
     * @return Config
     */
    public function isTaxConfig()
    {
        return $this->taxConfig;
    }

    /**
     * @return array
     */
    public function listPageDisable()
    {
        $list = $this->returnDisablePage();
        if ($list) {
            return explode(',', $list);
        } else {
            return [];
        }
    }

    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $configPath
     * @param string|int $scope
     * @return string
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param null $scope
     * @return string
     */
    public function returnPixelId($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/general/pixel_id',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return mixed
     */
    public function returnDisablePage($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/disable_code',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isProductView($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/product_view',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isCategoryView($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/category_view',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isInitiateCheckout($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/initiate_checkout',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isPurchase($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/purchase',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isAddToWishList($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/add_to_wishlist',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isAddToCart($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/add_to_cart',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isRegistration($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/registration',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isSubscribe($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/subscribe',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return bool
     */
    public function isSearch($scope = null)
    {
        return $this->scopeConfig->getValue(
            'devhub_facebook_event_tracker/event_tracking/search',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @param null $scope
     * @return mixed
     */
    public function isIncludeTax($scope = null)
    {
        return $this->scopeConfig->getValue(
            'tax/calculation/price_includes_tax',
            ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * Add slashes to string and prepares string for javascript.
     *
     * @param string $str
     * @return string
     */
    public function escapeSingleQuotes($str)
    {
        return str_replace("'", "\'", $str);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * @param array $data
     * @return string
     */
    public function getPixelHtml($data = false)
    {
        $json = 404;
        if ($data) {
            $json =$this->serializes($data);
        }

        return $json;
    }

    /**
     * @param mixed $product
     * @return mixed|string
     * @throws NoSuchEntityException
     */
    public function getProductPrice($product)
    {
        switch ($product->getTypeId()) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice($product);
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product);
                break;
            default:
                $price = $this->getFinalPrice($product);
        }

        return $price;
    }

    /**
     * Returns bundle product price.
     *
     * @param Product $product
     * @return string
     * @throws NoSuchEntityException
     */
    private function getBundleProductPrice($product)
    {
        $includeTax = (bool) $this->getDisplayTaxFlag();

        return $this->getFinalPrice(
            $product,
            $product->getPriceModel()->getTotalPrices(
                $product,
                'min',
                $includeTax,
                1
            )
        );
    }

    /**
     * @param  Product $product
     * @return string
     * @throws NoSuchEntityException
     */
    private function getConfigurableProductPrice($product)
    {
        if ($product->getFinalPrice() === 0) {
            $simpleCollection = $product->getTypeInstance()
                ->getUsedProducts($product);

            foreach ($simpleCollection as $simpleProduct) {
                if ($simpleProduct->getPrice() > 0) {
                    return $this->getFinalPrice($simpleProduct);
                }
            }
        }

        return $this->getFinalPrice($product);
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getGroupedProductPrice($product)
    {
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');

        $minPrice = INF;
        foreach ($assocProducts as $assocProduct) {
            $minPrice = min($minPrice, $this->getFinalPrice($assocProduct));
        }

        return $minPrice;
    }

    /**
     * Returns final price.
     *
     * @param Product $product
     * @param string $price
     * @return string
     * @throws NoSuchEntityException
     */
    private function getFinalPrice($product, $price = null)
    {
        $price = $this->resultPriceFinal($product, $price);

        $productType = $product->getTypeId();
        if ($productType != 'configurable' && $productType != 'bundle') {
            if ($this->getDisplayTaxFlag() && !$this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    false,
                    false
                );
            }
        }

        if ($productType != 'bundle') {
            if (!$this->getDisplayTaxFlag() && $this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    true,
                    false
                );
            }
        }

        return $price;
    }

    /**
     * @param Product $product
     * @param float|int $price
     * @return float
     * @throws NoSuchEntityException
     */
    private function resultPriceFinal($product, $price)
    {
        if ($price === null) {
            $price = $product->getFinalPrice();
        }

        if ($price === null) {
            $price = $product->getData('special_price');
        }
        $productType = $product->getTypeId();
        if (($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode())
            && $productType != 'configurable'
        ) {
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }
        return $price;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    private function getDisplayTaxFlag()
    {
        if ($this->taxDisplayFlag === null) {
            $flag = $this->isTaxConfig()->getPriceDisplayType($this->getStoreId());
            if ($flag == 1) {
                $this->taxDisplayFlag = 0;
            } else {
                $this->taxDisplayFlag = 1;
            }
        }

        return $this->taxDisplayFlag;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    private function getCatalogTaxFlag()
    {
        if ($this->taxCatalogFlag === null) {
            $this->taxCatalogFlag = (int) $this->isIncludeTax();
        }

        return $this->taxCatalogFlag;
    }

    /**
     * @return StoreInterface|Store|null
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore();
        }

        return $this->store;
    }

    /**
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->getStore()->getId();
        }

        return $this->storeId;
    }

    /**
     * Return base currency code
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getBaseCurrencyCode()
    {
        if ($this->baseCurrencyCode === null) {
            $this->baseCurrencyCode = strtoupper(
                $this->getStore()->getBaseCurrencyCode()
            );
        }

        return $this->baseCurrencyCode;
    }

    /**
     * Return current currency code
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrencyCode()
    {
        if ($this->currentCurrencyCode === null) {
            $this->currentCurrencyCode = strtoupper(
                $this->getStore()->getCurrentCurrencyCode()
            );
        }

        return $this->currentCurrencyCode;
    }
}
