<?php
/**
 * ComboDeals product selection
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option_Selection extends Mage_Adminhtml_Block_Widget
{
    /**
     * Initialize combodeal option selection block
     */
    public function _construct()
    {
        $this->setTemplate('arvato/combodeals/tab/option/selection.phtml');
    }

    /**
     * Return field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'combodeals_selection';
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'combodeals_selections';
    }

    /**
     * Retrieve delete button html
     *
     * @return string
     */
    public function getSelectionDeleteButtonHtml()
    {
        if($this->getProduct()->getId() == NULL) {
            return $this->getChildHtml('selection_delete_button');
        }
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
     * Return search url
     *
     * @return string
     */
    public function getSelectionSearchUrl()
    {
        $storeId = $this->getRequest()->getParam('store');
        return $this->getUrl('*/comboDeals_selection/search', array('store' => $storeId));
    }

    /*
     * Gets the HTML for the discount type drop down list
     * 
     * @return string
     */
    public function getDiscountTypeSelectHtml()
    {
        $selectionDiscountTypes = array(
            array('label' => 'None', 'value' => Arvato_ComboDeals_Model_Product_Discount::TYPE_NONE),
            array('label' => 'Fixed', 'value' => Arvato_ComboDeals_Model_Product_Discount::TYPE_FIXED),
            array('label' => 'Percent', 'value' => Arvato_ComboDeals_Model_Product_Discount::TYPE_PERCENT),
            array('label' => 'Free', 'value' => Arvato_ComboDeals_Model_Product_Discount::TYPE_FREE),
        );

        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId() . '_{{parentIndex}}_{{index}}_discount_type',
                'class' => 'select select-selection-role required-option-select',
                'extra_params' => 'onchange="comboDealSelection.addPriceRangeClass(event)"',
            ))
            ->setName($this->getFieldName() . '[{{parentIndex}}][{{index}}][discount_type]')
            ->setOptions($selectionDiscountTypes);

        return $select->getHtml();
    }

    /**
     * Prepare selection block layout
     *
     * @return Mage_Adminhtml_Block_Widget
     */
    protected function _prepareLayout()
    {
        $this->setChild('selection_delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete'),
                    'class' => 'delete icon-btn',
                    'on_click' => 'comboDealSelection.remove(event)'
                ))
        );
        return parent::_prepareLayout();
    }
}