<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Model;

use Magento\Framework\Model\AbstractModel;
use Thecommerceshop\Map\Api\Data\DiscountInterface;

class Discount extends AbstractModel implements DiscountInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Thecommerceshop\Map\Model\ResourceModel\Discount::class);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

     /**
     * @inheritDoc
     */
    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountType($DiscountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $DiscountType);
    }

     /**
     * @inheritDoc
     */
    public function getDiscount()
    {
        return $this->getData(self::DISCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscount($Discount)
    {
        return $this->setData(self::DISCOUNT, $Discount);
    }
}

