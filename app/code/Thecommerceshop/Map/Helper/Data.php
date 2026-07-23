<?php

namespace Thecommerceshop\Map\Helper;


use Magento\Customer\Model\Session as CustomerSession;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $quickviewOptions;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var string
     */
    public $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     /**
     * @var MapCategoryId
     */
    private $_MapCategoryId = false;

    /**
     * @var \Thecommerceshop\Map\Model\DiscountRepository
     */
    protected $discountRepo;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $priceCurrency;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CustomerSession $customerSession,
        \Thecommerceshop\Map\Model\DiscountRepository $discountRepo,
        \Magento\Directory\Model\Currency $priceCurrency
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->urlInterface = $context->getUrlBuilder();
        $this->_localeDate = $localeDate;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->customerSession = $customerSession;
        $this->discountRepo = $discountRepo;
        $this->priceCurrency = $priceCurrency;

    }

    /**
     * Btn Text color
     *
     * @return mixed|string
     */
    public function getBtnTextColor()
    {
        $color = $this->scopeConfig->getValue(
            'tcs_quickview/success_popup_design/button_text_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $color = ($color == '') ? '' : $color;
        return $color;
    }

    /**
     * Btn background
     *
     * @return mixed|string
     */
    public function getBtnBackground()
    {
        $backGround = $this->scopeConfig->getValue(
            'tcs_quickview/success_popup_design/background_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $backGround = ($backGround == '') ? '' : $backGround;
        return $backGround;
    }

    /**
     * Button text
     *
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getButtonText()
    {
        $buttonText = $this->scopeConfig->getValue(
            'tcs_quickview/success_popup_design/button_text',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $buttonText = ($buttonText == '') ? __('Quick View') : $buttonText;
        return $buttonText;
    }

    /**
     * Enabled module
     *
     * @return mixed|string
     */
    public function enabled()
    {
        $isEnabled = $this->scopeConfig->getValue(
            'tcs_quickview/general/enable_product_listing',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $isEnabled = ($isEnabled == '') ? '' : $isEnabled;
        return $isEnabled;
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        $productUrl = $this->urlInterface->getUrl('tcs_quickview/catalog_product/view/');
        return $productUrl;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = $this->urlInterface->getUrl();
        return $baseUrl;
    }

    /**
     * Get Enable Remove Reivews
     *
     * @return string
     */
    public function getRemoveReview()
    {
        $data = $this->scopeConfig->getValue(
            'tcs_quickview/general/remove_reviews',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $data;
    }

    /**
     * Get Enable Remove More Information
     *
     * @return string
     */
    public function getRemoveMoreInfo()
    {
        $data = $this->scopeConfig->getValue(
            'tcs_quickview/general/remove_product_tab',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $data;
    }

    /**
     * Get sku template
     *
     * @return string
     */
    public function getSkuTemplate()
    {
        $this->quickviewOptions = $this->scopeConfig->getValue(
            'tcs_quickview',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $removeSku = $this->quickviewOptions['general']['remove_sku'];
        if (!$removeSku) {
            return 'Magento_Catalog::product/view/attribute.phtml';
        }

        return '';
    }

    /**
     * Get Custom css
     *
     * @return string
     */
    public function getCustomCSS()
    {
        $this->quickviewOptions = $this->scopeConfig->getValue(
            'tcs_quickview',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return trim($this->quickviewOptions['general']['custom_css']);
    }

    /**
     * Get close seconds
     *
     * @return int
     */
    public function getCloseSeconds()
    {
        $this->quickviewOptions = $this->scopeConfig->getValue(
            'tcs_quickview',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return trim($this->quickviewOptions['general']['close_quickview']);
    }

    /**
     * Get product Image
     *
     * @return mixed
     */
    public function getProductImageWrapper()
    {
        $result = $this->scopeConfig->getValue('tcs_quickview/seting_theme/product_image_wrapper', $this->scopeStore);
        if ($result == null) {
            $result = 'product-image-wrapper';
        }
        return $result;
    }

    /**
     * Get Product Item Info
     *
     * @return mixed|string
     */
    public function getProductItemInfo()
    {
        $result = $this->scopeConfig->getValue('tcs_quickview/seting_theme/product_item_info', $this->scopeStore);
        if ($result == null) {
            $result = 'product-item-info';
        }
        return $result;
    }

    public function getPriceLabelBlockId()
    {
        $result = $this->scopeConfig->getValue('tcs_quickview/general/map_price_label', $this->scopeStore);
        if ($result == null) {
            $result = 'product_sales_label';
        }
        return $result;
    }

    public function getAddToCartText()
    {
        $result = $this->scopeConfig->getValue('tcs_quickview/general/map_button_text', $this->scopeStore);
        if ($result == null) {
            $result = 'View Lowest Price';
        }
        return $result;
    }

    public function getMapAttribute()
    {
        return 'map_price';
    }

    public function getMapCategoryId()
    {
        $result = $this->scopeConfig->getValue('tcs_quickview/general/map_category_id', $this->scopeStore);
        if ($result != null) {
            $this->_MapCategoryId = (int)$result;
        }
        return $this->_MapCategoryId;
    }
    

    public function getRuleDiscount($product) {
        $productRepository = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository');

        // $id = 8225;
        // $product = $productRepository->getById($id);

        $priceRules = null; 
        $price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(); 
        $storeId = $product->getStoreId(); 
        $dateTs = $this->_localeDate->scopeTimeStamp($storeId); 
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId(); 
    
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId(); 
        } else { 
            $customerGroupId = $this->_customerSession->getCustomerGroupId(); 
        } 
    
        $resource = $this->_objectManager->create('\Magento\CatalogRule\Model\ResourceModel\Rule');

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('Price ' . $price);
    
        //$rules = $resource->getRulesFromProduct($dateTs, $websiteId, $customerGroupId,  $product->getId());

        $collection = $this->_objectManager->get(\Magento\SalesRule\Model\ResourceModel\Rule\Collection::class)
                    ->addIsActiveFilter()
                    ->addFieldToFilter('coupon_type',1)
                    ->setOrder('sort_order','asc');
        $quoteId = $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getQuoteId();
        $real_quote = $this->_objectManager->create(\Magento\Quote\Model\Quote::class)->load($quoteId);
        $product = $this->_objectManager->get(\Magento\Quote\Model\Quote\Item::class)->setQuote($real_quote)->setProduct($product);
        $product->setAllItems(array($product));
        $product->getProduct()->setProductId($product->getEntityId());
        $ruleData = [];
        foreach($collection as $rule)
        {
            if ($rule->getActions()->validate($product))
            {
                $logger->info($rule->getId().'/'.$rule->getName().'/'.$rule->getData('discount_amount'));
                $ruleData[] = $rule->getData('discount_amount');//$rule->getId().'/'.$rule->getName().'/'.$rule->getData('discount_amount');
            }
        }
        return $ruleData;
        
    }

    /**
     * Map cart rule discount amount
     *
     * @return string
     */
    public function getRuleDiscountforCustomAmount($product_id = null, $regularPrice=null)
    {
        if($product_id == null){
            return false;
        }
        if($regularPrice == null){
            return false;
        }
        $specialPrice = 0;

        try {
            $ruleData = $this->discountRepo->get($product_id);
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
                }
            }
        } catch (\Exception $e) {
            // No discount rule exists for this product, return 0 (no discount)
            return 0;
        }

        return $specialPrice;
    }
}
