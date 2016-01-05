<?php
/**
 * Combodeal Product Price Model
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 */
class Arvato_ComboDeals_Model_Product_Price extends Mage_Bundle_Model_Product_Price
{
    /**
     * Calculate final price of selection 
     * with take into discount type
     * with take into account tier price
     *
     * @param  Mage_Catalog_Model_Product $bundleProduct
     * @param  Mage_Catalog_Model_Product $selectionProduct
     * @param  float $bundleQty
     * @param  float $selectionQty
     * @param  bool $multiplyQty
     * @param  bool $takeTierPrice
     * @return float
     */
    public function getSelectionFinalTotalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty,
                                                $multiplyQty = true, $takeTierPrice = true)
    {
        if (is_null($selectionQty)) {
            $selectionQty = $selectionProduct->getSelectionQty();
        }

        if ($selectionProduct->getDiscountType() == Arvato_ComboDeals_Model_Product_Discount::TYPE_PERCENT) { // percent
            $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
            $price = $price - ($price * ($selectionProduct->getDiscountAmount() / 100));

        } else if ($selectionProduct->getDiscountType() == Arvato_ComboDeals_Model_Product_Discount::TYPE_FIXED) { // fixed
            $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
            $price = $price - $selectionProduct->getDiscountAmount();

        } else if ($selectionProduct->getDiscountType() == Arvato_ComboDeals_Model_Product_Discount::TYPE_FREE) { // free
            $price = 0;

        } else { // none
            $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
        }

        $price = $this->getLowestPrice($bundleProduct, $price, $bundleQty);

        if ($multiplyQty) {
            $price *= $selectionQty;
        }

        return $price;
    }
}
