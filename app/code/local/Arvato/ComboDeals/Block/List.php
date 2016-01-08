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
        $collection = Mage::getResourceModel('combodeals/option_collection')
                ->setStoreIdFilter($storeId)
                ->setDealDateFilter()
                ->setSortByTimeLeft()
                ->setStatusFilter();
        $this->setCollection($collection);
    }
    
    /**
     *  Prepare block for the pager and set the options collection
     * 
     * @return Arvato_ComboDeals_Block_List
     */
    protected function _prepareLayout()
    {
       if (Mage::helper('combodeals')->isEnableDedicatedPage()) {
            parent::_prepareLayout();

            $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
            $pager->setAvailableLimit(array(5 => 5, 10 => 10, 20 => 20, 'all' => 'All'));
            $pager->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
            return $this;
        }
    }
    
    /*
     * Prepare pager html for use in template
     * 
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
     * Returns deal timer block html
     *
     * @param Arvato_ComboDeals_Model_Option $option
     * @param int $count
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

        if (!empty($selections)) {
            foreach ($selections as $selection) {
                $productPrice = $priceHelper->getRegularProductPrice($selection, $inclTax);
                $total += $priceHelper->getDiscountedPrice($selection, $option, $productPrice);
            }
        }

        return $total;
    }
}    