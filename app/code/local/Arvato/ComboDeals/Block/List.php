<?php
/**
 * Combo Deal Product listing page
 *
 * @category   Arvato
 * @package    Arvato_Combodeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_List extends Mage_Core_Block_Template
{
    /**
     * @var Arvato_ComboDeal_Helper_Price
     */
    protected $priceHelper;
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $coreHelper;
    
    private $_options;
    
    /**
     * Set options collection
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $storeId = Mage::app()->getStore()->getStoreId();

        $collection = Mage::getResourceModel('combodeals/option_collection');
                ->setStoreIdFilter($storeId)
                ->setDealDateFilter()
                ->setSortByTimeLeft()
                ->setStatusFilter();
        $this->setCollection($collection);
    }
    
    /**
     *  Prepare toolbar and set to collection
     *
     * @return Arvato_ComboDeals_Block_List
    */

    protected function _prepareLayout()
    {

        if (Mage::helper('combodeals')->isEnableDedicatedPage())
        {
            parent::_prepareLayout();

            $toolbar = $this->getToolbarBlock();
            // called prepare sortable parameters
            $collection = $this->getCollection();

            // set collection to toolbar and apply sort
            $toolbar->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
            $this->getCollection()->load();
            return $this;
        }
    }

    /**
     *  Prepare block for the toolbar
     *
     * @return Arvato_ComboDeals_Block_List
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->createBlock('combodeals/toolbar', microtime());
        return $block;
    }

    /**
     *  return tollbar
     *
     * @return Arvato_ComboDeals_Block_List
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }


    /**
     * Get all the available combodeals
     * 
     * @param array $optionIds
     * @return Arvato_ComboDeals_Model_Option
     */
    public function getOptions($optionIds)
    {
         if (empty($this->_options)) {   
            $helper = Mage::helper("combodeals/option");
            $this->_options = $helper->getAllCombodeals($optionIds, $this->getCollection());
        }
        return $this->_options;
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
        $inclTax = $priceHelper->displayIncludingTax();

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
        $inclTax = $priceHelper->displayIncludingTax();
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