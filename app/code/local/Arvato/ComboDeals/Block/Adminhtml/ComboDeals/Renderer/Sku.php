<?php

/**
 * Adminhtml combo deal grid block
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Renderer_Sku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /*
     * Gets the concatenated sku's of the combodeal items 
     * 
     * @param $row int
     * @return string
     */

    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $productIds = explode(',', $value);
        $productsCollection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToSelect('sku ');

        $productsCollection->getSelect()
                ->columns(array('included_sku' => new Zend_Db_Expr
                            ("IFNULL(GROUP_CONCAT(DISTINCT CONCAT(`e`.`sku`)"
                            . " SEPARATOR ' , '), '')")));
        $includedSkus = $productsCollection->getFirstItem()->getIncludedSku();
        return $includedSkus;
    }

}

?>
