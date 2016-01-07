<?php
/**
 * ComboDeals product option
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option extends Mage_Adminhtml_Block_Widget
{
    /**
     * Form element
     *
     * @var Varien_Data_Form_Element_Abstract|null
     */
    protected $_element = null;

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
    protected function _construct()
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

    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set option element
     *
     * @param Varien_Data_Form_Element_Abstract $element
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
        if ($this->getOptions() || $this->getProduct()->getStoreId() != Arvato_ComboDeals_Helper_Option::STORE_ID_ZERO) {
            return;
        }
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
        if ($this->getOptions() || $this->getProduct()->getStoreId() != Arvato_ComboDeals_Helper_Option::STORE_ID_ZERO) {
            return;
        }
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
        return ($this->getProduct()->getStoreId() == Arvato_ComboDeals_Helper_Option::STORE_ID_ZERO);
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
                    'on_click'   => 'comboDealSelection.showSearch(event)',
                    'class' => 'add'
                )));

        $this->setChild('close_search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'id'    => $this->getFieldId().'_{{index}}_close_button',
                    'label'     => Mage::helper('combodeals')->__('Close'),
                    'on_click'   => 'comboDealSelection.closeSearch(event)',
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

    /**
     * Get store object of curently edited product
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store
     */
    protected function getStore($storeId)
    {
        return Mage::app()->getStore($storeId);
    }

    /**
     * Get store wise price format
     * 
     * @param decimal $price
     * @param int $storeId
     * @return string
     */
    public function getFormatPrice($price, $storeId)
    {
        return Mage::helper('core')->currencyByStore($price, $this->getStore($storeId), true, false);
    }
}