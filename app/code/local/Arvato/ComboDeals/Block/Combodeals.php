<?php
/**
 * Combodeals Product block
 *
 * @category   Combodeals
 * @package    Arvato_Combodeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Combodeals extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{
    private $_options;
    
    /**
     * Initialize block's cache
     */
   
    protected function _toHtml()
    {
        if ($this->_hasProductComboDeals() &&  Mage::helper("combodeals")->isEnableModuleOutput())
        {
            return parent::_toHtml();
        }
    }
    
    
    /*
     * Check if the product have combo deals at all
     * 
     * @return bool
     */
    private function _hasProductComboDeals()
    {
        $options = $this->getOptions();

        return !empty($options) && count($options) > 0;
    }

    /**
     * Prepare collection with new products
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }
    
    /*
     * Get the product object
     * 
     * return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $widgetProductId = $this->getData('product_id');

        // widget or block mode
        if (!empty($widgetProductId))
        {
            $product = Mage::getModel('catalog/product')
                ->load($widgetProductId);

            if (!$this->hasData('product'))
            {
                $this->setData('product', $product);
            }
            return $product;
        }
        else
        {
            return parent::getProduct();
        }
    }

    /**
     * Retrieve list of combodeal product options
     *
     * @return array
     */
    public function getOptions()
    { 
        if (empty($this->_options)) {   
            $helper = Mage::helper("combodeals/option");
            $this->_options = $helper->getComboDealProducts($this->getProduct());
        }
        return $this->_options;
    }

    /*
     * Get Combo deals add to cart Url
     * 
     * @param int $optionId
     * @return string $url
     */
    public function getComboDealAddToCartUrl($optionId)
    {
        $url = $this->getUrl('combodeals/cart/add',
            array(
                'product_id' => $this->getProduct()->getId(),
                'option_id'  => $optionId
            ));

        return $url;
    }

    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Arvato_ComboDeals_Model_Option $option
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getDealPriceHtml($product, $option, $displayMinimalPrice = false, $idSuffix = '')
    {
        $block = $this->getLayout()->createBlock('combodeals/price')
            ->setProduct($product)
            ->setOption($option)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix);

        return $block->toHtml();
    }
    
    
    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Arvato_ComboDeals_Model_Option $option
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getDealTimerHtml($option, $count)
    {        
        $block = $this->getLayout()->createBlock('combodeals/timer')
            ->setOption($option)
            ->setCount($count);    
        return $block->toHtml();
    }

    /*
     * Calculates the total minimum product price without the combo deal
     * 
     * @param Arvato_ComboDeals_Model_Option $option
     * @return float $total
     */
    public function getTotalPrice($option)
    {
        /* @var $priceHelper Arvato_ComboDeals_Helper_Price */
        $priceHelper = Mage::helper('combodeals/price');
        $product = $this->getProduct();
        $inclTax = $priceHelper->displayIncludingTax();

        $total = $priceHelper->getRegularProductPrice($product, $inclTax);

        $selections = $option->getSelections();

        if (!empty($selections)){
            foreach ($selections as $selection)
            {
                $total += $priceHelper->getRegularProductPrice($selection, $inclTax);
            }
        }

        return $total;
    }

    /*
     * Calculates the total discounted price (when the deal gets applied)
     * 
     * @param  Arvato_ComboDeals_Model_Option $option
     * @return float $total
     */
    public function getTotalDiscountedPrice($option)
    {
        /* @var $priceHelper Arvato_ComboDeals_Helper_Price */
        $priceHelper = Mage::helper('combodeals/price');
        $product = $this->getProduct();
        $inclTax = $priceHelper->displayIncludingTax();

        $productPrice = $priceHelper->getRegularProductPrice($product, $inclTax);
        $total = $priceHelper->getDiscountedPrice($product, $option, $productPrice);

        $selections = $option->getSelections();

        if (!empty($selections))
        {
            foreach ($selections as $selection)
            {
                $productPrice = $priceHelper->getRegularProductPrice($selection, $inclTax);
                $total += $priceHelper->getDiscountedPrice($selection, $option, $productPrice);
            }
        }

        return $total;
    }
    
    /*
     * Get the combo deal product stock status
     * 
     * @param Arvato_ComboDeals_Model_Option $option
     * @return  
     */
    public function getDealStockStatus($option)
    {
        $selections = $option->getSelections();
        $in_stock = array();
        if (!empty($selections)) {
            $in_stock = array();
            foreach ($selections as $selection) {
                if ($selection->getIsSalable() && Mage::helper('uandi_arvato')->isInStock($selection)) {
                    if (Mage::helper('uandi_arvato')->isFewLeft($selection)) {
                        $in_stock[] = 'fewleft';
                    } else {
                        $in_stock[] = 'instock';
                    }
                } else {
                    $in_stock[] = 'outstock';
                }
            }
        }
        return $in_stock;
    }
}

