<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */

class Arvato_ComboDeals_Model_Selection extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('combodeals/selection');
        parent::_construct();
    }

    /*
    * determins the discounted price
    */
    public function getDiscountedPrice($parentOption, $minimalProductPrice)
    {
        // init
        $discountedPrice = $minimalProductPrice;

        /* @var $option Arvato_ComboDeal_Model_Option */
        if($parentOption  && $this->isActionProduct())
        {
            $option = $parentOption;
            $amount = $option->getAmount();
            $discountType = $option->getDiscountType();

            if($discountType == Arvato_ComboDeals_Model_Option::DISCOUNT_TYPE_PERCENT)
            {
                $discountedPrice = $discountedPrice- $discountedPrice * $amount / 100;
            }

            if($discountType == Arvato_ComboDeals_Model_Option::DISCOUNT_TYPE_FIXED)
            {
                $discountedPrice = $discountedPrice- $amount;
            }


        }

        return $discountedPrice;
    }

}
