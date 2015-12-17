<?php
/**
 * ComboDeals selection product grid
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Option_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize product selection grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('combodeals_selection_search_grid');
        $this->setRowClickCallback('cdSelection.productGridRowClick.bind(cdSelection)');
        $this->setCheckboxCheckCallback('cdSelection.productGridCheckboxCheck.bind(cdSelection)');
        $this->setRowInitCallback('cdSelection.productGridRowInit.bind(cdSelection)');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Return grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/comboDeals_selection/grid', array('index' => $this->getIndex(), 'productss' => implode(',', $this->_getProducts())));
    }

    /**
     * Retrieve store
     *
     * @return string|integer|Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _beforeToHtml()
    {
        $this->setId($this->getId().'_'.$this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');

        return parent::_beforeToHtml();
    }

    /**
     * Prepare grid collection
     */
    protected function _prepareCollection()
    {
        // Load the actual products by the given IDs with qty
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addStoreFilter()
            ->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1 ) AND ( {{table}}.qty>0',
                'inner')
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->addAttributeToFilter('visibility', array('in'=>Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds()))
            ->addAttributeToFilter('status', array('in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds()));
        if ($products = $this->_getProducts()) {
            $collection->addIdFilter($this->_getProducts(), true);
        }

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'index'     => 'name',
            'column_css_class'=> 'name'
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'column_css_class'=> 'sku'
        ));
        
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'column_css_class'=> 'price'
        ));
        
        $this->addColumn('qty', array(
            'header'    => Mage::helper('catalog')->__('Qty'),
            'width'     => '80px',
            'index'     => 'qty',
            'column_css_class'=> 'qty',
            'type'      => 'number'
        ));

        $this->addColumn('is_selected', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_selected',
            'align'     => 'center',
            'values'    => $this->_getSelectedProducts(),
            'index'     => 'entity_id',
        ));

        return parent::_prepareColumns();
    }
    
    /**
     * Return selected products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products', array());
        return $products;
    }

    /**
     * Return products
     *
     * @return array
     */
    protected function _getProducts()
    {
        if ($products = $this->getRequest()->getPost('products', null)) {
            return $products;
        } else if ($productss = $this->getRequest()->getParam('productss', null)) {
            return explode(',', $productss);
        } else {
            return array();
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    private function _getProduct()
    {
        return Mage::registry('current_product');
    }
}