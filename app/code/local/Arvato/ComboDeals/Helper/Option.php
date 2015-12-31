<?php
/**
 * ComboDeals option and selections colletion
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Helper_Option extends Mage_Core_Helper_Abstract
{
    /**
     * Store ID zero
     */
    const STORE_ID_ZERO = 0;

    /**
     * Cache key for Options Collection
     *
     * @var string
     */
    protected $_keyOptionsCollection = '_cache_instance_comboDeals_options_collection';

    /**
     * Cache key for Selections Collection
     *
     * @var string
     */
    protected $_keySelectionsCollection = '_cache_instance_comboDeals_selections_collection';
    
    
    /**
     * Cache key for Parent Product Collection
     *
     * @var string
     */
    protected $_keyParentsCollection = '_cache_instance_comboDeals_parents_collection';

    /**
     * Get Options with attached Selections collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    public function getOptions($product)
    {
        $storeId = $product->getStoreId();
        $product->setStoreFilter($storeId, $product);

        $optionId = null; // load all combo deal options for the given product and store
        $optionCollection = $this->_getOptionsCollection($product, $optionId, $storeId);

        $selectionCollection = $this->_getSelectionsCollection(
            $this->_getOptionsIds($product),
            $product,
            $storeId
        );

        $options = $optionCollection->appendSelections($selectionCollection);
        $return_options = array();

        foreach ($options as $option) {
            // set date format to "%m/%e/%Y"
            $option = $this->getFormatDate($option);

            $return_options[] = $option;
        }

        return $return_options;
    }

    /*
     * Gets the option with the given id incl. the selections
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param int $optionId
     * 
     * @return array
     */
    public function getOption($product, $optionId)
    {
        $storeId = $product->getStoreId();
        $product->setStoreFilter($storeId, $product);

        $optionCollection = $this->_getOptionsCollection($product, $optionId, $storeId);

        $selectionCollection = $this->_getSelectionsCollection(
            $this->_getOptionsIds($product),
            $product,
            $storeId
        );

        $options = $optionCollection->appendSelections($selectionCollection);

        return array_shift($options);
    }

    /**
     * Retrieve combodeal option collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $optionId
     * @param int $storeId The store id/scope for the combo deal option (default: 0)
     *
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection
     */
    private function _getOptionsCollection($product, $optionId, $storeId)
    {
        if (!$product->hasData($this->_keyOptionsCollection))
        {
            // load the combo deal options
            /** @var Arvato_ComboDeals_Model_Resource_Option_Collection $optionsCollection */
            $optionsCollection = Mage::getModel('combodeals/option')->getResourceCollection();

            // filter by product id
            $productId = $product->getId();
            $optionsCollection->setProductIdFilter($productId);

            // filter by store id if one is supplied
            if ($storeId !== null) {
                $optionsCollection->setStoreIdFilter($storeId);
                if(!$optionsCollection->getData()) {
                    $optionsCollection->getSelect()->reset(Zend_Db_Select::WHERE);
                    $optionsCollection->setProductIdFilter($productId)
                        ->setStoreIdFilter(self::STORE_ID_ZERO);
                }
            }
            
            // filter by option id if one is supplied
            if ($optionId !== null)
            {
                $optionsCollection->setOptionIdFilter($optionId);
            }

            // store the result at the product
            $product->setData($this->_keyOptionsCollection, $optionsCollection);
        }

        return $product->getData($this->_keyOptionsCollection);
    }

    /**
     * Retrieve combodeal options ids
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    private function _getOptionsIds($product)
    {
        $storeId = $product->getStoreId();
        return $this->_getOptionsCollection($product, null, $storeId)->getAllIds();
    }

    /**
     * Retrieve selections collection based on used options
     *
     * @param array $optionIds
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId The store id/scope for the combo deal option (default: 0)
     *
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    private function _getSelectionsCollection($optionIds, $product, $storeId)
    {
        $keyOptionIds = (is_array($optionIds) ? implode('_', $optionIds) : '');
        $key = $this->_keySelectionsCollection . $keyOptionIds;
        if (!$product->hasData($key))
        {
            $selectionsCollection = Mage::getResourceModel('combodeals/selection_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setFlag('require_stock_items', true)
                ->setFlag('product_children', true)
                ->setPositionOrder()
                ->setOptionIdsFilter($optionIds);
            $product->setData($key, $selectionsCollection);
        }
        return $product->getData($key);
    }

    /**
     * Retrieve combodeal option with formatted from and to date
     *
     * @param Arvato_ComboDeals_Model_Option $option
     * @return Arvato_ComboDeals_Model_Option $option
     */
    public function getFormatDate($option)
    {
        //set formatted from date i.e. 12/9/2015
        $fromDate = $this->prepareFormatDate($option->getFromDate());
        $option->setFromDate($fromDate);

        $toDate = $this->prepareFormatDate($option->getToDate());
        $option->setToDate($toDate);
        
        return $option;
    }

    /**
     * prepare formatted from and to date
     *
     * @param string $date
     * @return string $date
     */
    public function prepareFormatDate($date)
    {
        //prepare formatted date i.e. 12/9/2015
        $date = Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false);

        return $date;
    }
    
    /*
     * Retrive the combo deal products
     * 
     * @param Mage_Catalog_Model_Product $product 
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    public function getComboDealProducts($product)
    {
        $storeId = $product->getStoreId();
        $product->setStoreIdFilter($storeId, $product);
        $optionIds = $this->getAssociatedOptions($product, $storeId);
        if (empty($optionIds)) {
            return;
        } else {
            $selectionCollection = $this->_getSelectionsCollection($optionIds, $product, $storeId);
            $return_options = array();
            foreach ($optionIds as $optionId) {
                $optionsCollection = Mage::getModel('combodeals/option')->getResourceCollection();
                // filter by option id if one is supplied
                if ($optionId !== null) {
                    $optionsCollection->setOptionIdFilter($optionId);
                }
                $optionsCollection->setDealDateFilter()
                        ->setSortByTimeLeft()
                        ->setStatusFilter();
                $options = $optionsCollection->appendSelections($selectionCollection, false, true);
                foreach ($options as $option) {
                    $return_options[] = $option;
                }
            }
            return $return_options;
        }
    }
    
    /*
     * Retrieve the option ids
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId
     * @return array $optionIds
     */
    public function getAssociatedOptions($product, $storeId)
    {        
        // filter by product id
        $productId = $product->getId();
        $optionIds = array();
        $parentCollection = Mage::getResourceModel('combodeals/selection_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addAttributeToSelect('selection' . 'option_id')
                ->setFlag('require_stock_items', true)
                ->setPositionOrder()
                ->setStoreIdFilter($storeId)
                ->setProductIdsFilter($productId);
        foreach ($parentCollection->getItems() as $_selection) {
            $optionIds[] = $_selection->getOptionId();
        }
        return $optionIds;
    }  
    
    
    
    public function getAllCombodeals()
    {
        // Gets the current store's id
        $storeId = Mage::app()->getStore()->getStoreId();
        $optionCollection = Mage::getResourceModel('combodeals/option')
                ->setStoreIdFilter($storeId)
                ->setDealDateFilter()
                ->setSortByTimeLeft()
                ->setStatusFilter();
                
     
    }
}