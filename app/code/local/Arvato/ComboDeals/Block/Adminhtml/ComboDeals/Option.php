<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option extends Mage_Adminhtml_Block_Widget
{

    /**
     * List of combodeal options
     *
     * @var array|null
     */
    protected $_options = null;

    /**
     * combodeal option renderer class constructor
     *
     * Sets block template and necessary data
     */
    public function __construct()
    {
        $this->setTemplate('arvato/combodeals/tab/option.phtml');
    }

    public function getFieldId()
    {
        return 'combodeals_option';
    }

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

    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function isMultiWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getCloseSearchButtonHtml()
    {
        return $this->getChildHtml('close_search_button');
    }

    public function getAddSelectionButtonHtml()
    {
        return $this->getChildHtml('add_selection_button');
    }

    /**
     * Retrieve list of combodeal options
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

    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()
                ->getBlock('admin.combodeals')
                ->getChild('add_button')->getId();
        return $buttonId;
    }

    public function getOptionDeleteButtonHtml()
    {
        return $this->getChildHtml('option_delete_button');
    }

    public function getSelectionHtml()
    {

        return $this->getChildHtml('selection_template');
    }

    public function getTypeSelectHtml()
    {
        $discountTypes =  array(
            array('label' => 'Fixed Amount','value' => 'by_fixed'),
            array('label' =>'Percent','value' =>'by_percent'),
        );

        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{index}}_discount_type',
                'class' => 'select select-product-option-type required-option-select',
            ))
            ->setName($this->getFieldName().'[{{index}}][discount_type]')
            ->setOptions($discountTypes);

        return $select->getHtml();
    }


    public function isDefaultStore()
    {
        return ($this->getProduct()->getStoreId() == '0');
    }

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
