<?php
/**
 * Combo Deal Product Price Helper
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Helper_Price extends Mage_Core_Helper_Abstract {

    /**
     * @var Mage_Tax_Helper_Data
     */
    protected $taxHelper;

    public function __construct() 
    {
        $this->taxHelper = Mage::helper('tax');
    }

    /**
     * Gets the minimal regular catalog price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $inclTax
     * @return float $regularProductPrice
     */
    public function getRegularProductPrice($product, $inclTax)
    {
        $selectionModel = Mage::getModel('combodeals/selection')->setData($product->getData());
    //    print_R($selectionModel);exit;
        $qty = $selectionModel->getMinimumQty();
        $_regularPrice = $this->taxHelper->getPrice($product, $product->getPrice(), $inclTax);
        $_finalPrice = $this->taxHelper->getPrice($product, $product->getFinalPrice(), $inclTax);
        $minimalPrice = min($_regularPrice, $_finalPrice);
        $regularProductPrice = $minimalPrice * $qty;
        return $regularProductPrice;
    }

    /**
     * Determines the discounted price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Arvato_ComboDeals_Model_Option $option
     * @return float $discountedPrice
     */
    public function getDiscountedPrice($product, $option) 
    {
        $selectionModel = Mage::getModel('combodeals/selection')->setData($product->getData());
        if ($option) {
            $amount = $selectionModel->getDiscountAmount();
            $regularPrice = $this->getRegularProductPrice($product, $this->displayIncludingTax());
            switch ($discountType = $selectionModel->getDiscountType()) {
                case Arvato_ComboDeals_Model_Selection::DISCOUNT_TYPE_PERCENT:
                    $storeId = $product->getStoreId();
                    $getGrossPrice = $this->taxHelper->discountTax($storeId);
                    $applyDiscountOnPrice = $this->getRegularProductPrice($product, $getGrossPrice);
                    $discountedPrice = $regularPrice - round($applyDiscountOnPrice * $amount / 100, 2);
                    break;
                case Arvato_ComboDeals_Model_Selection::DISCOUNT_TYPE_FIXED:
                    $discountedPrice = $regularPrice - $amount;
                    break;
                case Arvato_ComboDeals_Model_Selection::DISCOUNT_TYPE_FREE:
                    $discountedPrice = 0;
                    break;
                default:
                    $discountedPrice = $regularPrice;
                    break;
            }
        }
        if ($discountedPrice < 0) {
            $discountedPrice = 0;
        }
        return $discountedPrice;
    }

    /**
     * Display price with tax if tax applicable
     * 
     * @return bool
     */
    public function displayIncludingTax() {
        return ($this->taxHelper->displayPriceIncludingTax() || $this->taxHelper->displayBothPrices());
    }

}
