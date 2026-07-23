<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Api\Data;

interface DiscountSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Discount list.
     * @return \Thecommerceshop\Map\Api\Data\DiscountInterface[]
     */
    public function getItems();

    /**
     * Set product_id list.
     * @param \Thecommerceshop\Map\Api\Data\DiscountInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

