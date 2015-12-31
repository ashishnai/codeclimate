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
    
     /*
     * Check if the product have combo deals at all
     * 
     * @return bool
     */
    private function _hasCategoryProducts()
    {
        $options = $this->getOptions();

        return !empty($options) && count($options) > 0;
    }
    
    
    public function getOptions()
    {
          if (empty($this->_options)) {   
            $helper = Mage::helper("combodeals/option");
            $this->_options = $helper->getAllCombodeals();
        }
        return $this->_options;
    }
   
}    