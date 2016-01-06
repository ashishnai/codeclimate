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
     * Flag True
     */
    const TRUE = 1;

    /**
     * Flag False
     */
    const FALSE = 0;

    /**
     * Is used default store options
     *
     * @var int
     */
    protected $_isUsedDefaultStoreOption = null;

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
        $this->_isUsedDefaultStoreOption = self::FALSE;

        // load the combo deal options
        /** @var Arvato_ComboDeals_Model_Resource_Option_Collection $optionsCollection */
        $optionsCollection = Mage::getModel('combodeals/option')->getResourceCollection();

        // filter by product id
        $productId = $product->getId();
        $optionsCollection->setProductIdFilter($productId);

        // filter by store id if one is supplied
        if ($storeId !== null) {
            $optionsCollection->setStoreIdFilter($storeId);
        }

        // set default store data is store wise data not present
        if(!$optionsCollection->getData()) {
            $this->_isUsedDefaultStoreOption = self::TRUE;
            $optionsCollection = $this->_getDefaultOptionsCollection($productId);
        }

        // filter by option id if one is supplied
        if ($optionId !== null)
        {
            $optionsCollection->setOptionIdFilter($optionId);
        }

        return $optionsCollection;
    }

    /**
     * Retrieve default store combodeal option collection
     *
     * @param int $productId
     *
     * @return array
     */
    private function _getDefaultOptionsCollection($productId)
    {
        $optionsCollection = Mage::getSingleton('combodeals/option')->getResourceCollection();
        $optionsCollection->setProductIdFilter($productId)
            ->setStoreIdFilter(self::STORE_ID_ZERO);
        foreach ($optionsCollection as $option) {
            $option->setData('option_id', null);
        }
        return $optionsCollection;
    }

    /**
     * Is default store options used
     *           
     * @return int
     */
    public function isUsedDefaultStoreOptions()
    {
        return $this->_isUsedDefaultStoreOption;
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
        $selectionsCollection = Mage::getResourceModel('combodeals/selection_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setFlag('require_stock_items', true)
            ->setFlag('product_children', true)
            ->setPositionOrder()
            ->setStoreId($storeId)
            ->setOptionIdsFilter($optionIds);

        return $selectionsCollection;
    }

    /**
     * Retrieve combodeal option with formatted from and to date
     *
     * @param Arvato_ComboDeals_Model_Option $option
     * @return Arvato_ComboDeals_Model_Option $option
     */
    public function getFormatDate($option)
    {
        //set formatted from date i.e. 12/9/2015 1:00 AM
        $fromDate = $option->getFromDate();
        $fromDate = $this->prepareFormatDate($fromDate);
        $option->setFromDate($fromDate);

        //set formatted to date i.e. 12/9/2015 1:00 AM
        $toDate = $option->getToDate();
        $toDate = $this->prepareFormatDate($toDate);
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
        //prepare formatted date i.e. 12/9/2015 1:00 AM
        $date = Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);

        return $date;
    }
    
    /*
     * Retrive the combo deal products
     * 
     * @param Mage_Catalog_Model_Product $product 
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection $return_options
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
        if($limit = Mage::helper('combodeals')->getProductLimit()){
            $parentCollection->setDealLimit($limit);
        }
        foreach ($parentCollection->getItems() as $_selection) {
            $optionIds[] = $_selection->getOptionId();
        }
        return $optionIds;
    }  
    
    
    /*
     * Retrieve all the active combodeals
     * 
     * @param array $optionIds
     * @param Arvato_ComboDeals_Model_Resource_Option_Collection $optionsCollection
     * @return Arvato_ComboDeals_Model_Resource_Option_Collection $return_options
     */
    public function getAllCombodeals($optionIds, $optionsCollection)
    {
        $selectionsCollection = Mage::getResourceModel('combodeals/selection_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setFlag('require_stock_items', true)
                ->setFlag('product_children', true)
                ->setStoreIdFilter($storeId)
                ->setOptionIdsFilter($optionIds)
                ->setPositionOrder();
        $options = $optionsCollection->appendSelections($selectionsCollection, false, true);
        $return_options = array();

        foreach ($options as $option) {
            // set date format to "%m/%e/%Y"
            $option = $this->getFormatDate($option);
            $return_options[] = $option;
        }
        return $return_options;
    }
}