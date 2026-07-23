<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface DiscountRepositoryInterface
{

    /**
     * Save Discount
     * @param \Thecommerceshop\Map\Api\Data\DiscountInterface $discount
     * @return \Thecommerceshop\Map\Api\Data\DiscountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Thecommerceshop\Map\Api\Data\DiscountInterface $discount
    );

    /**
     * Retrieve Discount
     * @param string $productId
     * @return \Thecommerceshop\Map\Api\Data\DiscountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($productId);

    /**
     * Retrieve Discount matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Thecommerceshop\Map\Api\Data\DiscountSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Discount
     * @param \Thecommerceshop\Map\Api\Data\DiscountInterface $discount
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Thecommerceshop\Map\Api\Data\DiscountInterface $discount
    );

    /**
     * Delete Discount by ID
     * @param string $productId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($productId);
}

