<?php
/**
 * Adminhtml product edit tabs
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_Tab extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_product = null;

    public function __construct()
    {
        parent::__construct();
        $this->setSkipGenerateContent(true);
        $this->setTemplate('arvato/combodeals/tab.phtml');
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/comboDeals_product_edit/form', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Check block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getCompositeReadonly();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getAddSelectionButtonHtml()
    {
        return $this->getChildHtml('add_selection_button');
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    public function getFieldSuffix()
    {
        return 'product';
    }
    
    public function getFieldId()
    {
        return 'combodeals_option';
    }

    public function getFieldName()
    {
        return 'combodeals_options';
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getTabLabel()
    {
        return Mage::helper('combodeals')->__('Combo Deal Items');
    }

    public function getTabTitle()
    {
        return Mage::helper('combodeals')->__('Combo Deal Items');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        $product = $this->getProduct();
        if($product->getTypeId() != Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            return true;
        }
    }

    /**
     * Prepare layout
     *
     * @return Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle
     */
    protected function _prepareLayout()
    {
        $optionsBlock = $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_option', 'admin.combodeals.option');
        $this->setChild('options_box',$optionsBlock);

        return parent::_prepareLayout();
    }
   
}
