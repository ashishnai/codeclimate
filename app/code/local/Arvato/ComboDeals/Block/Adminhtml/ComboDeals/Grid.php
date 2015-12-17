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
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /*
     * Get store id from store view switcher selection
     * 
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Grid
     */

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /*
     * Prepare catalog product collection for combo deal products
     * 
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Grid
     */

    protected function _prepareCollection() {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('type_id', 'combodeal');

        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                    'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore
            );
            $collection->joinAttribute(
                    'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId()
            );
        } else {
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        }
        $collection->getSelect()
                ->joinLeft(array('coption' => Mage::getSingleton('core/resource')
                    ->getTableName('arvato_catalog_product_combodeal_option')), 'e.entity_id = coption.parent_id' . ' AND ' . 'coption.store_id=' . $store->getId(), array('from_date', 'to_date'))
                ->joinLeft(array('cselection' => Mage::getSingleton('core/resource')
                    ->getTableName('arvato_catalog_product_combodeal_selection')), 'coption.option_id = cselection.option_id' . ' AND ' . 'cselection.store_id=' . $store->getId(), array('product_id'))
                ->columns(array('included_sku' => new Zend_Db_Expr
                            ("IFNULL(GROUP_CONCAT(DISTINCT CONCAT(`cselection`.`product_id`)"
                            . " SEPARATOR ' , '), '')")))
                ->group('e.entity_id');
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    /*
     * Prepare column for filter
     * 
     * @param $column object
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Grid
     */

    protected function _addColumnFilterToCollection($column) {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /*
     * Prepare mass action for delete
     * 
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Grid
     */

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('combodeals')->__('ID'),
            'width' => '50px',
            'type' => 'number',
            'index' => 'entity_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
                'index' => 'custom_name',
            ));
        }
        $this->addColumn('sku', array(
            'header' => Mage::helper('combodeals')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
        ));

        $this->addColumn('from_date', array(
            'header' => Mage::helper('combodeals')->__('Start Date'),
            'width' => '60px',
            'type' => 'datetime',
            'index' => 'from_date',
            'filter_condition_callback' => array($this, 'filterCallbackFromDate'),
        ));

        $this->addColumn('to_date', array(
            'header' => Mage::helper('combodeals')->__('End Date'),
            'width' => '60px',
            'type' => 'datetime',
            'index' => 'to_date',
            'filter_condition_callback' => array($this, 'filterCallbackToDate'),
        ));


        $this->addColumn('included_sku', array(
            'header' => Mage::helper('combodeals')->__('Included SKUs'),
            'width' => '100px',
            'index' => 'included_sku',
            'filter_condition_callback' => array($this, 'filterCallbackIncludedSku'),
            'renderer' => 'Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Renderer_Sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('status', array(
            'header' => Mage::helper('combodeals')->__('Status'),
            'width' => '70px',
            'type' => 'options',
            'index' => 'status',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header' => Mage::helper('catalog')->__('Websites'),
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
            'getter' => 'getId',
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
            'is_system' => true,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('combodeals')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('combodeals')->__('XML'));
        return parent::_prepareColumns();
    }

    /*
     * Prepare mass action for delete
     * 
     * @return Arvato_ComboDeals_Block_Adminhtml_ComboDeals_Grid
     */

    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('catalog')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));
        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
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

    /*
     * Get Grid Url
     * 
     * @return string
     */

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /*
     * Get Row Url
     * 
     * @param $row obect
     * @return string
     */

    public function getRowUrl($row) {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
                    'store' => $this->getRequest()->getParam('store'),
                    'id' => $row->getId(),
                    'redirectBack' => 'combodealGrid')
        );
    }

    /*
     * Filter for included skus
     * 
     * @param $collection object, $column filter 
     * @return $collection object
     */

    public function filterCallbackIncludedSku($collection, $column) {
        $value = $column->getFilter()->getValue();
        $collection->addAttributeToFilter('sku', array('like' => '%' . $value . '%'));
        return $collection;
    }

    /*
     * Filter for Start Date
     * 
     * @param $collection object, $column filter
     * @return $collection object
     */

    public function filterCallbackToDate($collection, $column) {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
                ->prepareSqlCondition('coption.from_date', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
        return $collection;
    }

    /*
     * Filter for End Date
     * 
     * @param $collection object, $column filter
     * @return $collection object
     */

    public function filterCallbackFromDate($collection, $column) {

        if (!$column->getFilter()->getCondition()) {
            return;
        }
        $condition = $collection->getConnection()
                ->prepareSqlCondition('coption.to_date', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
        return $collection;
    }

}
