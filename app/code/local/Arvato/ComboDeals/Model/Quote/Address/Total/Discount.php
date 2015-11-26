<?php
class Arvato_ComboDeals_Model_Quote_Address_Total_Discount 
extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
     public function __construct()
    {
         $this -> setCode('discount_total');
         }
    /**
     * Collect totals information about discount
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */
     public function collect(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: collect($address);
         $items = $this->_getAddressItems($address);
         if (!count($items)) {
            return $this;
         }
         $quote= $address->getQuote();

		 //amount definition

         $discountAmount = 0.01;

         //amount definition

         $discountAmount = $quote -> getStore() -> roundPrice($discountAmount);
         $this -> _setAmount($discountAmount) -> _setBaseAmount($discountAmount);
         $address->setData('discount_total',$discountAmount);

         return $this;
     }
    
    /**
     * Add discount totals information to address object
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */
     public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: fetch($address);
         $amount = $address -> getTotalAmount($this -> getCode());
         if ($amount != 0){
             $address -> addTotal(array(
                     'code' => $this -> getCode(),
                     'title' => $this -> getLabel(),
                     'value' => $amount
                    ));
         }
        
         return $this;
     }
    
    /**
     * Get label
     * 
     * @return string 
     */
     public function getLabel()
    {
         return Mage :: helper('combodeals') -> __('Combi Deal Discount');
    }
}