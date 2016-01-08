<?php
/**
 * set discount on checkout 
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_SaveDiscountObserver 
{
    /**
     * set discount on total cart value
     *
     * @param Varien_Event_Observer $observer
     */
    public function setDiscount(Varien_Event_Observer $observer) 
    {
        $quote = $observer->getEvent()->getQuote();
        $quoteId = $quote->getId();
        $discountAmount = Mage::getModel('combodeals/product_price')->getTotalDiscount();
        $productDiscountAmount = Mage::getModel('combodeals/product_price')->getProductDiscount();

        if ($quoteId) {
            if ($discountAmount > 0) {
                $total = $quote->getBaseSubtotal();
                $quote->setSubtotal(0);
                $quote->setBaseSubtotal(0);

                $quote->setSubtotalWithDiscount(0);
                $quote->setBaseSubtotalWithDiscount(0);

                $quote->setGrandTotal(0);
                $quote->setBaseGrandTotal(0);

                $this->setQuoteTotal($quote, $discountAmount);

                foreach ($quote->getAllItems() as $item) {
                    if($item->getProductType() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
                        $item->setDiscountAmount($item->getDiscountAmount() + $productDiscountAmount[$item->getProductId()]);
                        $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $productDiscountAmount[$item->getProductId()])->save();
                    }
                }
            }
        }
    }

    /**
     * set quote total 
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param float $discountAmount
     */
    public function setQuoteTotal($quote, $discountAmount) 
    {
        $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
        foreach ($quote->getAllAddresses() as $address) {

            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);

            $address->collectTotals();

            $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
            $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

            $quote->setSubtotalWithDiscount(
                (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
            );
            $quote->setBaseSubtotalWithDiscount(
                (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
            );

            $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
            $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

            $quote->save();

            $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                    ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                    ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                    ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                    ->save();

            if ($address->getAddressType() == $canAddItems) {
                $this->setAddressTotal($address, $discountAmount);
            }//end: if
        } //end: foreach
        return $this;
    }

    /**
     * set quote total 
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param float $discountAmount
     */
    public function setAddressTotal($address, $discountAmount)
    {
        $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount);
        $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount);
        $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
        $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount);
        if ($address->getDiscountDescription()) {
            $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
            $address->setDiscountDescription($address->getDiscountDescription() . ', ' . Mage::helper('combodeals')->getDiscountLabel());
            $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
        } else {
            $address->setDiscountAmount(-($discountAmount));
            $address->setDiscountDescription(Mage::helper('combodeals')->getDiscountLabel());
            $address->setBaseDiscountAmount(-($discountAmount));
        }
        $address->save();
        return $this;
    }
}