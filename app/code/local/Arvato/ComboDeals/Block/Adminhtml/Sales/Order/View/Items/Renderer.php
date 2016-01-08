<?php
/**
 * Adminhtml sales order combodeal item renderer
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_Sales_Order_View_Items_Renderer
    extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer
{
    /**
     * Check whether to calculate child products 
     *
     * @return bool
     */
    public function isChildCalculated($item = null)
    {
        return false;
    }
}