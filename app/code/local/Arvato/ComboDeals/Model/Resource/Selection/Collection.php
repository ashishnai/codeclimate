<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
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
     * Set price format, thumbnail html, store id for each collection item when collection was loaded
     *
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_items) {
            foreach ($this->_items as $item) {
                $item->setPrice($item->getPrice());

                // set thumbnail html
                $item->setImage($this->getImageHtml($item));
                if($this->getStoreId()) {
                    $item->setStoreId($this->getStoreId());
                }
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
        $this->addAttributeToSelect('thumbnail');
        $this->getSelect()->join(array('selection' => $this->_selectionTable),
            'selection.product_id = e.entity_id',
            array('*')
        );
        $this->joinField(
                'qty', 
                'cataloginventory/stock_item',
                'ROUND(qty)',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
    }
    
    
    
    /**
     * Set store filter
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store
     */
    public function setStoreIdFilter($storeId)
    {
        if (!empty($storeId)) {
            $this->getSelect()->where('selection.store_id = (?)', $storeId);
        }
        return $this;
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
     * Apply product ids filter to collection
     *
     * @param array $productIds
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function setProductIdsFilter($productIds)
    {
        if (!empty($productIds)) {
            $this->getSelect()->where('selection.product_id IN (?)', $productIds);
        }
        
        return $this;
    }
    
     /**
     * Apply product ids filter to collection
     *
     * @param array $productIds
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function setExcludeProductIdsFilter($productIds)
    {
        if (!empty($productIds)) {
            $this->getSelect()->where('selection.product_id NOT IN (?)', $productIds);
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
    
    
    /**
     * Sets limit to the number o combodeal products to display
     * 
     * @param int
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setDealLimit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }

    /**
     * Get thumbnail html
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string $imageOut
     */
    public function getImageHtml($product)
    {
        try {
            $imagePath = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(80);
            $imageOut = sprintf('<img src="%s" width="80px"/>', $imagePath);
            return $imageOut;
        } catch (Exception $e) {
            return;
        }
    }
}