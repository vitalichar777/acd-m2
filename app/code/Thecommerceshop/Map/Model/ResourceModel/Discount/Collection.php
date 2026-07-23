<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Model\ResourceModel\Discount;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'product_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Thecommerceshop\Map\Model\Discount::class,
            \Thecommerceshop\Map\Model\ResourceModel\Discount::class
        );
    }
}

