<?php
/**
 * ComboDeals selection product grid thumbnail renderer
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Return thumbnail html
     *
     * @param Mage_Catalog_Model_Product $row
     * @return string $imageOut
     */
    public function render(Varien_Object $row)
    {
        try {
            $imagePath = Mage::helper('catalog/image')->init($row, 'thumbnail')->resize(80);
            $imageOut = sprintf('<img src="%s" width="80px"/>', $imagePath);
            return $imageOut;
        } catch (Exception $e) {
            return;
        }
    }
}