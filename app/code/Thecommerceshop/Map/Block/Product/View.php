<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Thecommerceshop\Map\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;

/**
 * Product View block
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class View extends AbstractProduct implements IdentityInterface
{
    /**
     * Magento string lib
     *
     * @var StringUtils
     */
    protected $string;

    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var PriceCurrencyInterface
     * @deprecated 102.0.0
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @var FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Thecommerceshop\Map\Model\DiscountRepository
     */
    protected $discountRepo;


    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param EncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface|PriceCurrencyInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        EncoderInterface $jsonEncoder,
        StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        \Thecommerceshop\Map\Model\DiscountRepository $discountRepo,
        array $data = []
    ) {
        $this->_productHelper = $productHelper;
        $this->urlEncoder = $urlEncoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->productTypeConfig = $productTypeConfig;
        $this->string = $string;
        $this->_localeFormat = $localeFormat;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        $this->discountRepo = $discountRepo;
        parent::__construct(
            $context,
            $data
        );
    }

    // @codingStandardsIgnoreEnd

    /**
     * Return wishlist widget options
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated 101.0.1
     */
    public function getWishlistOptions()
    {
        return ['productType' => $this->getProduct()->getTypeId()];
    }

    /**
     * Retrieve current product model
     *
     * @return Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        return false;
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = ActionInterface::PARAM_NAME_URL_ENCODED;
        $addUrlValue = $this->_urlBuilder->getUrl('*/*/*', ['_use_rewrite' => true, '_current' => true]);
        $additional[$addUrlKey] = $this->urlEncoder->encode($addUrlValue);

        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Get JSON encoded configuration which can be used for JS dynamic price calculation depending on product options
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getJsonConfig()
    {
        /* @var $product Product */
        $product = $this->getProduct();

        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat()
            ];
            return $this->_jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $priceInfo = $product->getPriceInfo();
        $tierPricesList = $priceInfo->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $tierPrice['price']->getValue() * 1;
        }
        $config = [
            'productId' => (int)$product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $priceInfo->getPrice('regular_price')->getAmount()->getValue() * 1,
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount() * 1,
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $priceInfo->getPrice('final_price')->getAmount()->getValue() * 1,
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];

        $responseObject = new DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isStartCustomization()
    {
        return $this->getProduct()->getConfigureMode() || $this->_request->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     *
     * Also restricts it by minimal qty.
     *
     * @param null|Product $product
     * @return int|float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Get container name, where product options should be displayed
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionsContainer()
    {
        return $this->getProduct()->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
    }

    /**
     * Check whether quantity field should be rendered
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function shouldRenderQuantity()
    {
        return !$this->productTypeConfig->isProductSet($this->getProduct()->getTypeId());
    }

    /**
     * Get Validation Rules for Quantity field
     *
     * @return array
     */
    public function getQuantityValidators()
    {
        $validators = [];
        $validators['required-number'] = true;
        return $validators;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIdentities()
    {
        $identities = $this->getProduct()->getIdentities();

        return $identities;
    }

    /**
     * Retrieve customer data object
     *
     * @return int
     */
    protected function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * Jone Encoder QuantityValidators
     *
     * @return string
     */
    public function jsonEncoderQuantityValidators()
    {
        return $this->_jsonEncoder->encode($this->getQuantityValidators());
    }

     /**
     * Map cart rule discount amount
     *
     * @return string
     */
    public function getRuleDiscount($_product)
    {
        $discountHtml = false;
        $specialPrice = $youSave = 0;
        if ($_product->getTypeId() == 'bundle') {
            $regularPrice = $_product->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
        } else {
            $regularPrice = $_product->getFinalPrice();
        }

        $ruleData =  $this->discountRepo->get($_product->getId());
        $distype = 1;
        if(isset($ruleData)){
            $CurrentCustomerGroup = 0;
            if($this->customerSession->getCustomer()->getId() > 0){
                $CurrentCustomerGroup = $this->customerSession->getCustomer()->getGroupId();
            }

            $ruleCustomerIds = explode(',',$ruleData->getCustomerGroup());
            $IsGroupValid = in_array($CurrentCustomerGroup, $ruleCustomerIds);
            $mapPrice = $ruleData->getDiscount();
            if($IsGroupValid && $mapPrice > 0){
                $disAmount = 0;
                if($ruleData->getDiscountType() == 'by_percent'){
                    $disAmount = $regularPrice * $mapPrice/100;
                    $distype = 1;
                }elseif($ruleData->getDiscountType() == 'by_fixed'){
                    $disAmount = $mapPrice;
                    $distype = 2;
                }
                $specialPrice = $regularPrice - $disAmount;
                $youSave = $regularPrice - $specialPrice;
                $discountHtml = "<div><span class='mPrice' data-price='" . $regularPrice . "'  data-distype='" . $distype . "'  data-disvalue='" . $mapPrice . "'>" . $this->priceCurrency->convertAndFormat($regularPrice, false) . "</span></div><span class='mSpecialPrice'>" . $this->priceCurrency->convertAndFormat($specialPrice, false) . "</span> </br> <span class='SavePriceText'> You Save: <span class='SavePrice'>" . $this->priceCurrency->convertAndFormat($youSave, false) . "</span>!</span>";
            }
        }
        return $discountHtml;
    }
}