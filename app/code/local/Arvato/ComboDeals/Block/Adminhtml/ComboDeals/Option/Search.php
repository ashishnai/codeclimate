<?php
/**
 * ComboDeals selection product search form
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option_Search extends Mage_Adminhtml_Block_Widget
{
    /**
     * Initialize product search form
     */
    public function _construct()
    {
        $this->setId('combodeals_option_selection_search');
        $this->setTemplate('arvato/combodeals/tab/option/search.phtml');
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('combodeals')->__('Please Select Products to Add');
    }

    /**
     * Produce buttons HTML
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'id'    => 'add_button_' . $this->getIndex(),
            'label' => Mage::helper('combodeals')->__('Add Selected Product(s) to Combo Deal'),
            'onclick' => 'cdSelection.productGridAddSelected(event)',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

    /**
     * Prepare Layout Content
     *
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option_Search_Grid
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_option_search_grid',
                'adminhtml.combodeals.option.search.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget
     */
    protected function _beforeToHtml()
    {
        $this->getChild('grid')->setIndex($this->getIndex())
            ->setFirstShow($this->getFirstShow());

        return parent::_beforeToHtml();
    }
}