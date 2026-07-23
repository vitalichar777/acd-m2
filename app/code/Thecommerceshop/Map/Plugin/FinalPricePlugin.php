<?php
namespace Thecommerceshop\Map\Plugin;

class FinalPricePlugin
{
    public function beforeSetTemplate(\Magento\Catalog\Pricing\Render\FinalPriceBox $subject, $template)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $enable=$objectManager->create('Thecommerceshop\Map\Helper\Data')->enabled();
        if ($enable) {
            if ($template == 'Magento_Bundle::product/price/final_price.phtml') {
                return ['Thecommerceshop_Map::product/price/final_price.phtml'];
            } 
            else
            {
                return [$template];
            }
        } else {
            return[$template];
        }
    }
}