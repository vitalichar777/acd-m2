<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Pdpnewdesign\Observer\Frontend\Layout;

class LoadBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $fullActionName = $observer->getData('full_action_name');
       if($fullActionName == 'catalog_product_view' || $fullActionName == 'tcs_quickview_catalog_product_view'){
            $layout = $observer->getData('layout');
            $layout->getUpdate()->addHandle('catalog_product_view_pdpnewdesign');
            $layout->getUpdate()->addHandle('catalog_product_view_type_bundle_pdpnewdesign');
       }
        return $this;
    }
}

