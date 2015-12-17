<?php
/**
 * ComboDeals product edit tab
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_Tab extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Link to currently editing product
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Initialize combodeal tab
     * 
     * Sets block template and necessary data
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSkipGenerateContent(true);
        $this->setTemplate('arvato/combodeals/tab.phtml');
    }

    /**
     * Return tab url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/comboDeals_product_edit/form', array('_current' => true));
    }

    /**
     * Return tab css class
     *
     * @return string
     */
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

    /**
     * Return add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    /**
     * Return add selection button html
     *
     * @return string
     */
    public function getAddSelectionButtonHtml()
    {
        return $this->getChildHtml('add_selection_button');
    }

    /**
     * Return option box html
     *
     * @return string
     */
    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    /**
     * Return field suffix
     *
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Return field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'combodeals_option';
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'combodeals_options';
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Return tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('combodeals')->__('Combo Deal Items');
    }

    /**
     * Return tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('combodeals')->__('Combo Deal Items');
    }

    /**
     * Can show tab?
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden?
     *
     * @return bool
     */
    public function isHidden()
    {
        $product = $this->getProduct();
        if($product->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            return false;
        }

        return true;
    }

    /**
     * Prepare tab layout
     *
     * @return Arvato_ComboDeals_Block_Adminhtml_Tab
     */
    protected function _prepareLayout()
    {
        $optionsBlock = $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_option', 'admin.combodeals.option');
        $this->setChild('options_box',$optionsBlock);

        return parent::_prepareLayout();
    }
}