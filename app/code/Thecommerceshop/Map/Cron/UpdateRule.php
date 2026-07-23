<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Cron;

use Thecommerceshop\Map\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollectionModel;
use Magento\Framework\App\ResourceConnection;
use Thecommerceshop\Map\Api\Data\DiscountInterfaceFactory;

class UpdateRule
{

    /**
     * logger
     */
    protected $logger;

    /**
     * MAP module helper
     */
    protected $_maphelper;

    /**
     * Product collection
     */
    protected $_productCollection;

    /**
     * Map product collection
     */
    protected $_mapCollection;

    /**
     * Product - category mapper collection
     */
    protected $_productCategory;

    /**
     * Rule collection
     */
    protected $_rulecollectionmodel;

    /**
     * Rule collection list
     */
    protected $_rulecollection;

    /**
     * resouce to connect DB
     */
    protected $_resourcecollection;

    /**
     * map_discount entity model factory
     */
    protected $_discountFactory;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Data $maphelper,
        Collection $productCollection,
        ProductCategoryList $productCategory,
        RuleCollectionModel $rulecollectionmodel,
        ResourceConnection $resourcecollection,
        DiscountInterfaceFactory $discountFactory,
    ){
        $this->logger = $logger;
        $this->_maphelper = $maphelper;
        $this->_productCollection = $productCollection;
        $this->_productCategory = $productCategory;
        $this->_rulecollectionmodel = $rulecollectionmodel;
        $this->_resourcecollection = $resourcecollection;
        $this->_discountFactory = $discountFactory;
    }

    /**
     * Execute the cron to update the discount rule data on map_discount table
     *
     * @return void
     */
    public function execute()
    {
        try {
            $productCollection = $this->getProductCollection();

            $connection = $this->_resourcecollection->getConnection();
            $discountTable = $this->_resourcecollection->getTableName('map_discount');

            $DiscountModel = $this->_discountFactory->create();
            $ProductIds = [];
            foreach($productCollection as $product){
                $ruleData = $this->getRuleData($product);
                //if(isset($ruleData['rule_id'])){
                $ruleData['rule_id'] = isset($ruleData['rule_id'])?$ruleData['rule_id']:0;
                $ruleData['discount_type'] = isset($ruleData['discount_type'])?$ruleData['discount_type']:'by_percent';
                $ruleData['discount'] = isset($ruleData['discount'])?$ruleData['discount']:0;
                $ruleData['customer_group'] = isset($ruleData['customer_group'])?$ruleData['customer_group']:'0';
                    $DiscountModel->load($product->getId());
                    if($DiscountModel->getProductId() && $DiscountModel->getProductId() == $product->getId()){
                        $DiscountModel->setSku($product->getSku())
                            ->setRuleId($ruleData['rule_id'])
                            ->setDiscountType($ruleData['discount_type'])
                            ->setDiscount($ruleData['discount'])
                            ->setCustomerGroup($ruleData['customer_group'])
                            ->save();
                    }else{
                        $sql = "insert into " . $discountTable . "(product_id,sku,rule_id,discount_type,discount,customer_group)values(".$product->getId().",'".$product->getSku()."',".$ruleData['rule_id'].", '".$ruleData['discount_type']."', ".$ruleData['discount'].",'".$ruleData['customer_group']."');";
                        $connection->query($sql);
                    }
               // }
                $ProductIds[] = $product->getId();
            }
            $Delsql = "delete from " . $discountTable . " where product_id not in(".implode(',', $ProductIds).")";
            $connection->query($Delsql);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * To get the Map price category id from the admin config
     * @return Int
     */
    protected function _getMapCategoryId(){
        return $this->_maphelper->getMapCategoryId();
    }

    /**
     * To load adn get the Map Price Category
     *
     */
    // protected function _getMapCategory(){
    //     $CategoryId = $this->_getMapCategoryId();
    //     $Mapcategory = $this->_category->load($CategoryId);
    //     return $Mapcategory;
    // }

    /**
     * To get Map product collection by the Map category
     *
     */
    public function getProductCollection(){
        if($this->_mapCollection == NULL){
            $this->_mapCollection = $this->_productCollection->addCategoriesFilter(['in'=>[$this->_getMapCategoryId()]]);
        }
        return $this->_mapCollection;
    }

    /**
     * To collect the Map product ids
     *
     */
    // public function getMapProductIds(){
    //     return $this->getProductCollection()->getAllIds();
    // }

    /**
     * To collect required or needed data from the Map product collection
     *
     */
    // public function getMapRequiredData(){
    //     $MapData = [];
    //     foreach($this->getProductCollection() as $product){
    //         $productId = $product->getId();
    //         $productSku = $product->getSku();
    //         $categoryIds = $this->_productCategory->getCategoryIds($productId);
    //         $category = [];
    //         if ($categoryIds) {
    //             $category = array_unique($categoryIds);
    //         }
    //         $MapData[$productId]= ['sku'=>$productSku, 'category_ids'=>$category];
    //     }
        
    //     return $MapData;
    // }

    /**
     * Used to collect the assigned category list of a product
     *
     */
    public function getProductCategories($product){
        $productId = $product->getId();
        $categoryIds = $this->_productCategory->getCategoryIds($productId);
        $category = [];
        if ($categoryIds) {
            $category = array_unique($categoryIds);
        }
        return $category;
    }
    
    /**
     * To get sales rule collection
     *
     */
    public function getRuleCollection(){
        if($this->_rulecollection == NULL){
            // $now = new \DateTime();
            $webSiteId = 1;
            $this->_rulecollection  = $this->_rulecollectionmodel->addIsActiveFilter()->addFieldToFilter('coupon_type',1)->setOrder('sort_order','asc')->addWebsiteFilter($webSiteId);
                // ->addFieldToFilter('from_date', ['lteq' => $now->format('Y-m-d H:i:s')])
                // ->addFieldToFilter('to_date', ['gteq' => $now->format('Y-m-d H:i:s')]);
        }
        return $this->_rulecollection;
    }

    /**
     * Used to create a data set to update on the map_discount table from the sales rule.
     *
     */
    public function getRuleData($product){
        $RuleCollection = $this->getRuleCollection();
        $ruleData = [];
        foreach($RuleCollection as $rule)
        {              
            $isvalid  = $this->_validate($rule, $product);
            if($isvalid){
                $customerGroupIds = implode(',', $rule->getCustomerGroupIds());
                $ruleData[] = ['rule_id'=>$rule->getId(), 'discount_type'=>$rule->getSimpleAction(), 'discount'=>$rule->getDiscountAmount(), 'sort_order'=>$rule->getSortOrder(), 'customer_group'=>$customerGroupIds];
            }            
        }
        if(count($ruleData) >= 1){
            array_multisort( array_column($ruleData, "sort_order"), SORT_ASC, $ruleData );
            return $ruleData[0];
        }
        return $ruleData;
    }

    /**
     * The sales rule validation of Action or condition for a product
     * Date and condition validation added, website added as filter of the rule collection;
     * User Group validation should do on the frontend - current plan
     */
    protected function _validate($rule, $product){
        $isValidRule = false;
        $actionData = $rule->getActions()->asArray();
        $isvalidDate = $this->_validateDate($rule);
        $isvalidCondition  = $this->_validateCondition($actionData, $product);
       if($isvalidDate && $isvalidCondition){
            $isValidRule = true;
       }
        return $isValidRule;
    }

    /**
     * Sales rule condition validation
     * Currently implemented for the SKU and Category_ids based on the current requirement
     * Currently implemented for "!=", "==", "()" operators based on the current requirement
     *
     */
    protected function _validateCondition($actionData, $product){
        if(isset($actionData['conditions'])){
            $allValid;
            $conditionValid = [];
            foreach($actionData['conditions'] as $key => $condition){
                if($condition['attribute'] == 'sku'){
                    if($condition['operator'] == "()"){
                        $conditionValid[] = in_array($product->getSku(), explode(',',$condition['value']))?'true':'false';
                    }elseif($condition['operator'] == "=="){
                        $conditionValid[] = ($product->getSku() == $condition['value'])?'true':'false';
                    }elseif($condition['operator'] == "!="){
                        $conditionValid[] = ($product->getSku() != $condition['value'])?'true':'false';
                    }
                }
                if($condition['attribute'] == 'category_ids'){
                    if($condition['operator'] == "=="){
                        $prdouctCategories = $this->getProductCategories($product);
                        $conditionCategories = explode(',',$condition['value']);
                        $resultCount = count(array_intersect($prdouctCategories, $conditionCategories));
                        if($resultCount > 0){
                            $conditionValid[] = 'true';
                        }else{
                            $conditionValid[] = 'false';
                        }
                    }elseif($condition['operator'] == "!="){
                        $prdouctCategories = $this->getProductCategories($product);
                        $conditionCategories = explode(',',$condition['value']);
                        $resultCount = count(array_intersect($prdouctCategories, $conditionCategories));
                        if($resultCount > 0){
                            $conditionValid[] = 'false';
                        }else{
                            $conditionValid[] = 'true';
                        }
                    }
                }
            }
            $isconditionValid = in_array('false', $conditionValid);
            if($isconditionValid){
                return false;
            }else{
                return true;
            }
        }
    }

    /**
     * Date validation for a rule.
     *
     */
    protected function _validateDate($rule){
        $now = new \DateTime();
        $currentDate = $now->format('Y-m-d');
        $fromDate = $rule->getFromDate();
        $toDate = $rule->getToDate();
        $status = false;
        if($fromDate <= $currentDate && ($toDate == NULL || $toDate == '' || $toDate >= $currentDate)){
            $status = true;
        }
        return $status;
    }
}