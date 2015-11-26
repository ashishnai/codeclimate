<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeal
 * @copyright   Copyright (c) 2013 arvato Hightech (http://arvato-hightech-ecommerce.com)
 */
class Arvato_ComboDeal_Helper_Option extends Mage_Core_Helper_Abstract
{
    /**
     * Cache key for Options Collection
     *
     * @var string
     */
    protected $_keyOptionsCollection = '_cache_instance_combodeal_options_collection';
    /**
     * Cache key for Selections Collection
     *
     * @var string
     */
    protected $_keySelectionsCollection = '_cache_instance_combodeal_selections_collection';
    
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
            $return_options[] = $option;
        }

        return $return_options;
    }
}
