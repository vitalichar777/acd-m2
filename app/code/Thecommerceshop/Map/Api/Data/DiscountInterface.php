<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Api\Data;

interface DiscountInterface
{

    const PRODUCT_ID = 'product_id';
    const SKU = 'sku';
    const RULE_ID ='rule_id';
    const DISCOUNT_TYPE = 'discount_type';
    const DISCOUNT = 'discount';

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Thecommerceshop\Map\Discount\Api\Data\DiscountInterface
     */
    public function setProductId($productId);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Thecommerceshop\Map\Discount\Api\Data\DiscountInterface
     */
    public function setSku($sku);

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $ruleId
     * @return \Thecommerceshop\Map\Discount\Api\Data\DiscountInterface
     */
    public function setRuleId($ruleId);

     /**
     * Get discount_type
     * @return string|null
     */
    public function getDiscountType();

    /**
     * Set discount_type
     * @param string $DiscountType
     * @return \Thecommerceshop\Map\Discount\Api\Data\DiscountInterface
     */
    public function setDiscountType($DiscountType);

     /**
     * Get discount
     * @return string|null
     */
    public function getDiscount();

    /**
     * Set discount
     * @param string $Discount
     * @return \Thecommerceshop\Map\Discount\Api\Data\DiscountInterface
     */
    public function setDiscount($Discount);
}

