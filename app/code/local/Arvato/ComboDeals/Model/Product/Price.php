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
     * total price for Bundle items
     *
     * @var float
     */
    protected $_totalPriceWithoutDisc = null;

    /**
     * total price for combo deal items with discount
     *
     * @var float
     */
    protected $_totalPriceWithDisc = null;

    /**
     * combodeal parent product discount array
     *
     * @var float
     */
    protected $_productDiscountArray = null;

    /**
     * Get Total price for Bundle items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|float $qty
     * @return float
     */
    public function getTotalBundleItemsPrice($product, $qty = null)
    {
        $priceWithoutDisc = 0.0;
        $priceWithDisc = 0.0;
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_selection_ids');
            if ($customOption) {
                $selectionIds = unserialize($customOption->getValue());
                $selections = $product->getTypeInstance(true)->getSelectionsByIds($selectionIds, $product);
                $selections->addTierPriceData();
                Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
                    'collection' => $selections,
                    'store_id' => $product->getStoreId(),
                ));
                foreach ($selections->getItems() as $selection) {
                    if ($selection->isSalable()) {
                        $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                        if ($selectionQty) {
                            $priceWithoutDisc += $this->getSelectionFinalTotalPrice($product, $selection, $qty,
                                $selectionQty->getValue());
                            $priceWithDisc += $this->getComboSelectionFinalTotalPrice($product, $selection, $qty,
                                $selectionQty->getValue());
                        }
                    }
                }
            }
        }

        $totalPriceWithoutDisc = $priceWithoutDisc * $qty;
        $totalPriceWithDisc = $priceWithDisc * $qty;

        $this->_totalPriceWithoutDisc += $totalPriceWithoutDisc;
        $this->_totalPriceWithDisc += $totalPriceWithDisc;

        $this->setPriceWithoutDiscount($this->_totalPriceWithoutDisc);
        $this->setPriceWithDiscount($this->_totalPriceWithDisc);

        $this->_productDiscountArray[$product->getId()] = $totalPriceWithoutDisc - $totalPriceWithDisc;
        $this->setProductDiscount($this->_productDiscountArray);

        return $priceWithoutDisc;
    }

    /**
     * Set total price for combodeal items with discount
     *
     * @param float $price
     */
    public function setPriceWithDiscount($price)
    {
        Mage::unregister('price_with_disc');
        Mage::register('price_with_disc', $price);
    }

    /**
     * Set total price for combodeal items without discount
     *
     * @param float $price
     */
    public function setPriceWithoutDiscount($price)
    {
        Mage::unregister('price_without_disc');
        Mage::register('price_without_disc', $price);
    }

    /**
     * Set each product discount
     *
     * @param array $discount
     */
    public function setProductDiscount($discount)
    {
        Mage::unregister('product_discount');
        Mage::register('product_discount', $discount);
    }

    /**
     * Return discount ammonunt
     *
     * @return float
     */
    public function getTotalDiscount()
    {
        return (Mage::registry('price_without_disc') - Mage::registry('price_with_disc'));
    }

    /**
     * Return discount ammonunt
     *
     * @return float
     */
    public function getProductDiscount()
    {
        return Mage::registry('product_discount');
    }

    /**
     * Calculate final price of selection 
     * without take into discount type
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
    public function getComboSelectionFinalTotalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty,
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

    /**
     * Calculate final price of selection
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

        $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
        $price = $this->getLowestPrice($bundleProduct, $price, $bundleQty);

        if ($multiplyQty) {
            $price *= $selectionQty;
        }

        return $price;
    }
}