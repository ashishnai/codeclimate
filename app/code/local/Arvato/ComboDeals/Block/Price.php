<?php
/**
 * Combo Deal Product price block
 *
 * @category   Arvato
 * @package    Arvato_Combodeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Price extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @var Arvato_ComboDeal_Helper_Price
     */
    protected $priceHelper;
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $coreHelper;
    private $regularPrice = null;
    private $discountedPrice = null;
    private $selectionQty = null;

    public function __construct()
    {
        parent::__construct();

        $this->priceHelper = Mage::helper('combodeals/price');
        $this->coreHelper = Mage::helper('core');
        $this->setSkipGenerateContent(true);
        $this->setTemplate('combodeals/price.phtml');
    }
    
    /**
     * Determines if the price is discounted or not
     * 
     * @return bool
     */
    public function isDiscountReceiver()
    {
        return $this->getDiscountedPrice() != $this->getRegularPrice();
    }


    /**
     * Gets formatted regular price
     * 
     * @return float
     */
    public function getFormattedRegularPrice()
    {
        return $this->coreHelper->currency($this->getRegularPrice(), true, false);
    }

    /**
     * Gets formatted discounted price
     * 
     * @return float
     */
    public function getFormattedDiscountedPrice()
    {
        return $this->coreHelper->currency($this->getDiscountedPrice(), true, false);
    }

    /**
     * Gets regular price
     * 
     * @return float
     */
    private function getRegularPrice()
    {
        if (is_null($this->regularPrice))
        {
            $this->regularPrice = $this->priceHelper->getRegularProductPrice($this->getProduct(), $this->priceHelper->displayIncludingTax());
        }
        return $this->regularPrice;
    }

    /**
     * Gets discounted price
     * 
     * @return float
     */
    private function getDiscountedPrice()
    {
        if (is_null($this->discountedPrice))
        {
            $this->discountedPrice = $this->priceHelper->getDiscountedPrice($this->getProduct(), $this->getOption());
        }
        return $this->discountedPrice;
    }
    
    /**
     * Gets minimum qty of the selected products
     * 
     * @return float
     */
    public function getSelectionQty()
    {
        if (is_null($this->selectionQty)){
            $this->selectionQty = $this->priceHelper->getSelectionQty($this->getProduct());
        }
        return $this->selectionQty;
    }
}
