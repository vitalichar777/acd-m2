<?php

namespace Thecommerceshop\Pdpnewdesign\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $storeScope;
    protected $categoryCollectionFactory;
    protected $_storeManager;

    const XML_PATH_AC_CAT_IDS = 'sizingcalculator/accalculator/categoryids';
    const XML_PATH_FURNACE_CAT_IDS = 'sizingcalculator/furnacecalculator/categoryids';
    const XML_PATH_DUCTLESS_CAT_IDS = 'sizingcalculator/ductlesscalculator/categoryids';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $storeManager;
    }

    public function getAcCalcCatIds() {
        $AcCats = $this->scopeConfig->getValue(self::XML_PATH_AC_CAT_IDS, $this->storeScope) ?? '';
        return explode(",",$AcCats);
    }

    public function getFurnaceCalcCatIds() {
        $FurnaceCats = $this->scopeConfig->getValue(self::XML_PATH_FURNACE_CAT_IDS, $this->storeScope) ?? '';
        return explode(",",$FurnaceCats);
    }

    public function getDuctlessCalcCatIds() {
        $DuctlessCats = $this->scopeConfig->getValue(self::XML_PATH_DUCTLESS_CAT_IDS, $this->storeScope) ?? '';
        return explode(",",$DuctlessCats);
    }
    
    public function getAllCalcCatIds(){
        $calcCatIds['ac'] = $this->getAcCalcCatIds();
        $calcCatIds['furnace'] = $this->getFurnaceCalcCatIds();
        $calcCatIds['ductless'] = $this->getDuctlessCalcCatIds();
        return $calcCatIds;
    }

    public function getWarrantyStatus($categoryIds){
        $Warrantycategories = $this->categoryCollectionFactory->create()->addAttributeToFilter('is_warranty_available', 1)->addAttributeToFilter('entity_id',array('in'=>$categoryIds))->setStore($this->_storeManager->getStore());
        if(count($Warrantycategories) > 0){
            return true;
        }else{
            return false;
        }
    }
}
