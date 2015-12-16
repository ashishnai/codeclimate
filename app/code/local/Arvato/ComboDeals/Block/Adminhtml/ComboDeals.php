<?php

/**
 * Catalog combo deals block
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals extends Mage_Adminhtml_Block_Widget_Container {

    /**
     * Set Grid Container Block
     */
    public function __construct() {
        parent::__construct();
        $this->setTemplate('arvato/combodeals/product.phtml');
    }

    /**
     * Prepare grid
     *
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals
     */
    protected function _prepareLayout() {
        $this->setChild('combodealgrid', $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_grid', 'combodealproduct.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml() {
        return $this->getChildHtml('combodealgrid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode() {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

}
