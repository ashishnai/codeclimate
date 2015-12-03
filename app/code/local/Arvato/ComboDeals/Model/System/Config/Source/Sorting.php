<?php
/**
 * Used in creating options for Soring options config value selection
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */

class Arvato_ComboDeals_Model_System_Config_Source_Sorting
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Maximum Savings')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Latest Deals')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Deals to Expire today')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('Maximum Savings'),
            1 => Mage::helper('adminhtml')->__('Latest Deals'),
            2 => Mage::helper('adminhtml')->__('Deals to Expire today'),
        );
    }

}