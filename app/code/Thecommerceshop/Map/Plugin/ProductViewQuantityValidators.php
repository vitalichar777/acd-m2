<?php
/**
 * Copyright © Thecommerceshop. All rights reserved.
 */
namespace Thecommerceshop\Map\Plugin;

use Magento\CatalogInventory\Api\StockRegistryInterface;

class ProductViewQuantityValidators
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Add stock quantity validators to the custom Map product view block
     *
     * @param \Thecommerceshop\Map\Block\Product\View $block
     * @param array $validators
     * @return array
     */
    public function afterGetQuantityValidators(
        \Thecommerceshop\Map\Block\Product\View $block,
        array $validators
    ) {
        try {
            $stockItem = $this->stockRegistry->getStockItem(
                $block->getProduct()->getId(),
                $block->getProduct()->getStore()->getWebsiteId()
            );

            $params = [];
            $params['minAllowed'] = (float)$stockItem->getMinSaleQty();
            if ($stockItem->getMaxSaleQty()) {
                $params['maxAllowed'] = (float)$stockItem->getMaxSaleQty();
            }
            if ($stockItem->getQtyIncrements() > 0) {
                $params['qtyIncrements'] = (float)$stockItem->getQtyIncrements();
            }
            $validators['validate-item-quantity'] = $params;
        } catch (\Exception $e) {
            // If stock item cannot be loaded, return validators as-is
        }

        return $validators;
    }
}
