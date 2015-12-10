<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Model_Resource_Selection_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Selection table name
     *
     * @var string
     */
    protected $_selectionTable;

    /**
     * Initialize collection
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setRowIdFieldName('selection_id');
        $this->_selectionTable = $this->getTable('combodeals/selection');
    }

    /**
     * Set store id for each collection item when collection was loaded
     *
     * @return void
     */
    public function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getStoreId() && $this->_items) {
            foreach ($this->_items as $item) {
                $item->setStoreId($this->getStoreId());
            }
        }
        return $this;
    }

    /**
     * Initialize collection select with qty
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(array('selection' => $this->_selectionTable),
            'selection.product_id = e.entity_id',
            array('*')
        );
        $this->joinField(
                'qty', 
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
    }

    /**
     * Apply option ids filter to collection
     *
     * @param array $optionIds
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function setOptionIdsFilter($optionIds)
    {
        if (!empty($optionIds)) {
            $this->getSelect()->where('selection.option_id IN (?)', $optionIds);
        }
        return $this;
    }

    /**
     * Apply selection ids filter to collection
     *
     * @param array $selectionIds
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function setSelectionIdsFilter($selectionIds)
    {
        if (!empty($selectionIds)) {
            $this->getSelect()->where('selection.selection_id IN (?)', $selectionIds);
        }
        return $this;
    }

    /**
     * Set position order
     *
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function setPositionOrder()
    {
        $this->getSelect()->order('selection.position asc')
            ->order('selection.selection_id asc');
        return $this;
    }
}