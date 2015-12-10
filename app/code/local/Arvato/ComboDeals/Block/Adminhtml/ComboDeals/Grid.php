<?php

/**
 * Adminhtml combo deal grid block
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('combodealGrid');
        $this->setDefaultSort('parent_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $store = $this->_getStore();
        $collection = Mage::getModel('combodeals/option')->getCollection();

        $productAttributes = array('product_name' => 'name', 'product_status' => 'status');
        foreach ($productAttributes as $alias => $attributeCode) {
            $tableAlias = $attributeCode . '_table';
            $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            $collection->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackendTable()), "main_table.parent_id = $tableAlias.entity_id AND "
                    . "$tableAlias.attribute_id={$attribute->getId()}", array($alias => 'value')
            );
        }
        $collection->getSelect()->joinLeft(
                        array('sku_table' => 'catalog_product_entity'), "main_table.parent_id = sku_table.entity_id ", array('combodeal_sku' => 'sku')
                )
                ->joinLeft(array('cselection' => 'arvato_catalog_product_combodeal_selection'), 'main_table.option_id = cselection.option_id', array('product_id' => 'product_id', 'discount_type' => 'discount_type'))
                ->joinLeft(array(
                    'cpe' => 'catalog_product_entity'), 'cpe.entity_id = cselection.product_id', array('sku')
                )
                ->columns(array('included_sku' => new Zend_Db_Expr
                            ("IFNULL(GROUP_CONCAT(DISTINCT CONCAT(`cpe`.`sku`)"
                            . " SEPARATOR ' , '), '')")))
                ->group('main_table.parent_id');
        //  ->columns('GROUP_CONCAT(DISTINCT (SELECT value FROM catalog_product_entity_decimal '
        //          . 'WHERE entity_id = cselection.product_id) SEPARATOR \', \') AS pprice')
        if ($store->getId()) {
            $collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
        }
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _addColumnFilterToCollection($column) {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites', 'catalog/product_website', 'website_id', 'parent_id=entity_id', null, 'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('combodeals')->__('ID'),
            'width' => '50px',
            'type' => 'number',
            'index' => 'option_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('combodeals')->__('Name'),
            'width' => '60px',
            'index' => 'product_name',
            'filter_index' => 'name_table.value',
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('combodeals')->__('SKU'),
            'width' => '60px',
            'index' => 'combodeal_sku',
            'filter_index' => 'sku_table.sku',
        ));
        $this->addColumn('discount_type', array(
            'header' => Mage::helper('combodeals')->__('Discount Type'),
            'width' => '70px',
            'index' => 'discount_type',
            'type' => 'options',
            'options' => Mage::getSingleton('combodeals/product_discount')->getOptionArray(),
        ));

        $this->addColumn('from_date', array(
            'header' => Mage::helper('combodeals')->__('Start Date'),
            'width' => '60px',
            'type' => 'datetime',
            'index' => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header' => Mage::helper('combodeals')->__('End Date'),
            'width' => '60px',
            'type' => 'datetime',
            'index' => 'to_date',
        ));


        $this->addColumn('included_sku', array(
            'header' => Mage::helper('combodeals')->__('Included SKUs'),
            'width' => '100px',
            'index' => 'included_sku',
            'filter_condition_callback' => array($this, 'filterCallbackIncludedSku')
        ));

        $store = $this->_getStore();
        $this->addColumn('status', array(
            'header' => Mage::helper('combodeals')->__('Status'),
            'width' => '70px',
            'type' => 'options',
            'index' => 'product_status',
            'filter_index' => 'status_table.value',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header' => Mage::helper('combodeals')->__('Websites'),
                'width' => '100px',
                'sortable' => false,
                'index' => 'websites',
                'type' => 'options',
                'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action', array(
            'header' => Mage::helper('combodeals')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getParentId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('combodeals')->__('View/Edit'),
                    'url' => array(
                        'base' => 'adminhtml/catalog_product/edit',
                        'params' => array('store' => $this->getRequest()->getParam('store'), 'redirectBack' => 'combodealGrid')
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('combodeals')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('combodeals')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('option_id');
        $this->getMassactionBlock()->setFormFieldName('products');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('catalog')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('adminhtml/catalog_product/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row) {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
                    'store' => $this->getRequest()->getParam('store'),
                    'id' => $row->getParentId())
        );
    }

    /*
     * Filter for included skus
     * @param $collection object, $column filter
     * @return $collection object
     */

    public function filterCallbackIncludedSku($collection, $column) {
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("cpe.sku LIKE '%" . $value . "%'");
        return $collection;
    }

}
