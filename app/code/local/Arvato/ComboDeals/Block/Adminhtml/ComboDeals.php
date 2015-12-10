<?php
/**
 * Catalog combo deals block
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Shireen Nimachwala <shireenn@cybage.com>
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set Grid Container Block
     */
    public function __construct()
    {  
        parent::__construct();
        $this->_blockGroup = 'combodeals';
        $this->_controller = 'adminhtml_comboDeals';
        $this->_headerText = Mage::helper('combodeals')->__('Combo Deal Products');
        $this->_removeButton('add');
    }
    
     /**
     * Prepare grid
     *
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals
     */
    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
            $this->_controller . '.grid') );
        return parent::_prepareLayout();
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }
}