<?php
namespace Thecommerceshop\Map\Block;


/**
 * Class Mapmsg
 * @package Thecommerceshop\Map\Block
 */
class Mapmsg extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     *call for price message value
     */
    const XML_PATH_PRICE_MESSAGE = 'tcs_quickview/general/map_price_label';

    /**
     * Wrap with standard required container
     *
     * @param string $html
     * @return string
     */
    public function wrapResult($html)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $callPriceHelper = $objectManager->get('Thecommerceshop\Map\Helper\Data');
        $callForPriceMessage = $callPriceHelper->getPriceLabel();
        $_product = $this->getSaleableItem();
        $callForPrice = $_product->getResource()->getAttribute($callPriceHelper->getMapAttribute())->getFrontend()->getValue($_product); //$callPriceHelper->showCallForPriceButton($this->getSaleableItem());
        if($callForPrice == ''):
            return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
                'data-role="priceBox" ' .
                'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
                '>' . $html . '</div>';
        else :
            return '<div class="price-box call-for-price">'.$callForPriceMessage.'</div>';
        endif;

    }

}
