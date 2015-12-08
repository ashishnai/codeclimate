<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option_Search extends Mage_Adminhtml_Block_Widget
{
    public function _construct()
    {
        $this->setId('combodeals_option_selection_search');
        $this->setTemplate('arvato/combodeals/tab/option/search.phtml');
    }

    public function getHeaderText()
    {
        return Mage::helper('combodeals')->__('Please Select Products to Add');
    }

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

    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_option_search_grid',
                'adminhtml.combodeals.option.search.grid')
        );
        return parent::_prepareLayout();
    }

    protected function _beforeToHtml()
    {
        $this->getChild('grid')->setIndex($this->getIndex())
            ->setFirstShow($this->getFirstShow());

        return parent::_beforeToHtml();
    }
}
