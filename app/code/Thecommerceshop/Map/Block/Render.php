<?php
namespace Thecommerceshop\Map\Block;

/**
 * Class Render
 * @package Thecommerceshop\Map\Block
 */
class Render extends \Magento\Catalog\Pricing\Render
{
    /**
     *call for price message value
     */
    const XML_PATH_PRICE_MESSAGE = 'tcs_quickview/general/map_price_label';

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $callPriceHelper = $objectManager->get('Thecommerceshop\Map\Helper\Data');
        $callForPriceMessage = $callPriceHelper->getPriceLabel();
        $product = $this->getProduct();
        $id = $product->getData('entity_id');
        $_product = $objectManager->get('Magento\Catalog\Model\Product')->load($id);
        $data = $_product->getData('call_for_price');
        if($data==0){
            /** @var PricingRender $priceRender */
            $priceRender = $this->getLayout()->getBlock($this->getPriceRender());
            if ($priceRender instanceof PricingRender) {
                $product = $this->getProduct();
                if ($product instanceof SaleableInterface) {
                    $arguments = $this->getData();
                    $arguments['render_block'] = $this;
                    return $priceRender->render($this->getPriceTypeCode(), $product, $arguments);
                }
            }
            return parent::_toHtml();
        }
        else
        {
            return '<div class="price-box call-for-price">'.$callForPriceMessage.'</div>';
        }

    }


}