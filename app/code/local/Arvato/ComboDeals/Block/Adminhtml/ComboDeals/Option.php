<?php
/**
 * ComboDeals product option
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option extends Mage_Adminhtml_Block_Widget
{

    /**
     * List of combodeals options
     *
     * @var array|null
     */
    protected $_options = null;

    /**
     * combodeals option renderer class constructor
     *
     * Sets block template and necessary data
     */
    public function __construct()
    {
        $this->setTemplate('arvato/combodeals/tab/option.phtml');
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
     * Retrieve Product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set option element
     *
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Get option element
     *
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Is multi websites?
     *
     * @return bool
     */
    public function isMultiWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
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
     * Return close search button html
     *
     * @return string
     */
    public function getCloseSearchButtonHtml()
    {
        return $this->getChildHtml('close_search_button');
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
     * Retrieve list of combodeals options
     *
     * @return array
     */
    public function getOptions()
    {
        if (!$this->_options) {
            $helper = Mage::helper("combodeals/option");
            $this->_options = $helper->getOptions($this->getProduct());
        }
        return $this->_options;
    }

    /**
     * Return product add button id
     *
     * @return string
     */
    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()
                ->getBlock('admin.combodeals')
                ->getChild('add_button')->getId();
        return $buttonId;
    }

    /**
     * Return option delete button html
     *
     * @return string
     */
    public function getOptionDeleteButtonHtml()
    {
        return $this->getChildHtml('option_delete_button');
    }

    /**
     * Return selection button html
     *
     * @return string
     */
    public function getSelectionHtml()
    {

        return $this->getChildHtml('selection_template');
    }

    /**
     * Is default store?
     *
     * @return bool
     */
    public function isDefaultStore()
    {
        return ($this->getProduct()->getStoreId() == '0');
    }

    /**
     * Prepare option block layout
     *
     * @return Mage_Adminhtml_Block_Widget
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_selection_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'id'    => $this->getFieldId().'_{{index}}_add_button',
                    'label'     => Mage::helper('combodeals')->__('Add Selection'),
                    'on_click'   => 'cdSelection.showSearch(event)',
                    'class' => 'add'
                )));

        $this->setChild('close_search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'id'    => $this->getFieldId().'_{{index}}_close_button',
                    'label'     => Mage::helper('combodeals')->__('Close'),
                    'on_click'   => 'cdSelection.closeSearch(event)',
                    'class' => 'back no-display'
                )));

        $this->setChild('option_delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete Deal'),
                    'class' => 'delete delete-product-option',
                    'on_click' => 'cdOption.remove(event)'
                ))
        );

        $this->setChild('selection_template',
            $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_option_selection')
        );

        return parent::_prepareLayout();
    }
}