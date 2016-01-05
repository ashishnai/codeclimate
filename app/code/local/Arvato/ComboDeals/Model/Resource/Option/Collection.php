<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_Resource_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * All item ids cache
     *
     * @var array
     */
    protected $_itemIds;

    /**
     * True when selections a
     *
     * @var bool
     */
    protected $_selectionsAppended = false;

    /**
     * Init model and resource model
     *
     */
    protected function _construct()
    {
        $this->_init('combodeals/option');
    }
    
    
    
    /**
     * Initialize collection option with product name, status
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');
        
        $productAttributes = array('name' => 'name', 'status' => 'status');
        foreach ($productAttributes as $alias=>$attributeCode) {
            $tableAlias = $attributeCode . '_table';
            $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackendTable()), "main_table.parent_id = $tableAlias.entity_id AND "
                    . "$tableAlias.attribute_id={$attribute->getId()}",array($alias=>'value')
            );
        }
    }

    /**
     * Sets store id filter
     *
     * @param int $storeId The id of the store scope
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setStoreIdFilter($storeId)
    {
        $this->addFieldToFilter('main_table.store_id', $storeId);
        return $this;
    }

    /**
     * Sets product id filter
     *
     * @param int $productId
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setProductIdFilter($productId)
    {
        $this->addFieldToFilter('main_table.parent_id', $productId);
        return $this;
    }

    /**
     * Sets option id filter
     *
     * @param int $optionId
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setOptionIdFilter($optionId)
    {
        $this->addFieldToFilter('main_table.option_id', $optionId);
        return $this;
    }
    
    /**
     * Sets the active deals filter
     * 
     * @param date|null $currentDate
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setDealDateFilter($currentDate=null)
    {
        if(is_null($currentDate)){
            $currentDate = Mage::getModel('core/date')->date('Y-m-d');
        }
        $this->addFieldToFilter('main_table.from_date', array('lteq' => $currentDate));
        $this->addFieldToFilter('main_table.to_date', array('gteq' => $currentDate));
        return $this;
                
    }
    
    /**
     * Sets filter on combo deal product status
     * 
     * 
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setStatusFilter()
    {
        $this->addFieldToFilter('status_table.value', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        return $this;
    }
            
    /**
     * Sets Combo deal Time left(Date Difference) sorting
     * 
     * 
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setSortByTimeLeft($currentDate=null)
    {
        if(is_null($currentDate)){
            $currentDate = Mage::getModel('core/date')->date('Y-m-d');
        }
        $this->getSelect()
                ->columns('DATEDIFF(main_table.to_date,'.$currentDate.') AS date_difference')
                ->order('date_difference desc');
        return $this;
    } 
    

    /**
     * Append all selections to options
     * stripBefore - indicates to reload
     * appendAll - indicates do we need to filter by saleable and required custom options
     *
     * @param Arvato_ComboDeals_Model_Resource_Selection_Collection $selectionsCollection
     * @param bool $stripBefore
     * @param bool $appendAll
     *
     * @return array
     */
    public function appendSelections($selectionsCollection, $stripBefore = false, $appendAll = true)
    {
        if ($stripBefore) {
            $this->_stripSelections();
        }
        if (!$this->_selectionsAppended) {

            foreach ($selectionsCollection->getItems() as $key => $_selection)
            {
                $_option = $this->getItemById($_selection->getOptionId());
                if ($_option && ($appendAll || ($_selection->isSalable() && !$_selection->getRequiredOptions()))) {
                    $_selection->setOption($_option);

                    // check if default store option used
                    $_selection = $this->checkUsedDefaultStoreOptions($_selection);
                    $_option->addSelection($_selection);
                }
                else {
                    $selectionsCollection->removeItemByKey($key);
                }
            }
            $this->_selectionsAppended = true;
        }
        return $this->getItems();
    }

    /**
     * Check and set null if default store options used
     * 
     * @param Arvato_ComboDeals_Model_Selection $_selection        
     * @param Arvato_ComboDeals_Model_Selection $_selection
     */
    protected function checkUsedDefaultStoreOptions($_selection)
    {
        if (Mage::helper('combodeals/option')->isUsedDefaultStoreOptions()) {
            $_selection->setData('selection_id', null);
            $_selection->setData('option_id', null);
        }
        return $_selection;
    }

    /**
     * Removes appended selections before
     *
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    protected function _stripSelections()
    {
        foreach ($this->getItems() as $option) {
            $option->setSelections(array());
        }
        $this->_selectionsAppended = false;
        return $this;
    }

    /**
     * Sets filter by option id
     *
     * @param array|int $ids
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function setIdFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('main_table.option_id', array('in' => $ids));
        } else if ($ids != '') {
            $this->addFieldToFilter('main_table.option_id', $ids);
        }
        return $this;
    }

    /**
     * Reset all item ids cache
     *
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function resetAllIds()
    {
        $this->_itemIds = null;
        return $this;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        if (is_null($this->_itemIds)) {
            $this->_itemIds = parent::getAllIds();
        }
        return $this->_itemIds;
    }
}