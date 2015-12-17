<?php
/**
 * ComboDeals option and selections colletion
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Helper_Option extends Mage_Core_Helper_Abstract
{
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
        // Check if each associated Product is in Stock
        foreach ($options as $option) {
            foreach ($option->getSelections() as $selection) {
                if (!$selection->getStockItem()->getIsInStock() && !Mage::app()->getStore()->isAdmin()) {
                    continue;
                }
            }
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
     * Retrieve bundle selections collection based on used options
     *
     * @param array $optionIds
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId The store id/scope for the combo deal option (default: 0)
     *
     * @return Arvato_ComboDeals_Model_Resource_Selection_Collection
     */
    private function _getSelectionsCollection($optionIds, $product, $storeId = 0)
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
                ->setStoreId($storeId)
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
        $fromDate = $option->getFromDate();
        $fromDate = Mage::helper('core')->formatDate($fromDate, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false);
        $option->setFromDate($fromDate);
        
        //set formatted to date i.e. 12/9/2015
        $toDate = $option->getToDate();
        $toDate = Mage::helper('core')->formatDate($toDate, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false);
        $option->setToDate($toDate);
        
        return $option;
    }
}