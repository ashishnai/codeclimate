<?php
/**
 * Adminhtml product type
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 */
class Arvato_ComboDeals_Model_Product_Type extends Mage_Bundle_Model_Product_Type
{
    const TYPE_COMBODEAL = 'combodeal';

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare of combodeal selections options.
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $result = Mage_Catalog_Model_Product_Type_Abstract::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        $selections = array();
        $product = $this->getProduct($product);
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
        $_appendAllSelections = (bool)$product->getSkipCheckRequiredOption() || $skipSaleableCheck;

        $options = $buyRequest->getBundleOption();
        $options = array_filter($options, 'intval');
        $qtys = $buyRequest->getBundleOptionQty();
        foreach ($options as $_optionId => $_selections) {
            if (empty($_selections)) {
                unset($options[$_optionId]);
            }
        }
        $optionIds = array_keys($options);

        if (empty($optionIds) && $isStrictProcessMode) {
            return Mage::helper('bundle')->__('Please select options for product.');
        }

        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
        $optionsCollection = Mage::helper('combodeals/option')->getOptions($product);
        $option = $optionsCollection[0];

        $selectionIds = array();

        foreach ($options as $optionId => $selectionId) {
            if (!is_array($selectionId)) {
                if ($selectionId != '') {
                    $selectionIds[] = (int)$selectionId;
                }
            } else {
                foreach ($selectionId as $id) {
                    if ($id != '') {
                        $selectionIds[] = (int)$id;
                    }
                }
            }
        }
        // If product has not been configured yet then $selections array should be empty
        if (empty($selectionIds)) {
            $selections = array();
        } else {
            $selections = $option->getSelections();
        }

        if (count($selections) > 0 || !$isStrictProcessMode) {
            $uniqueKey = array($product->getId());
            $selectionIds = array();

            // Shuffle selection array by option position
            usort($selections, array($this, 'shakeSelections'));

            foreach ($selections as $selection) {
                $qty = (float)$selection->getMinimumQty();

                $product->addCustomOption('selection_qty_' . $selection->getSelectionId(), $qty, $selection);
                $selection->addCustomOption('selection_id', $selection->getSelectionId());
                $product->addCustomOption('product_qty_' . $selection->getId(), $qty, $selection);

                /*
                 * Create extra attributes that will be converted to product options in order item
                 * for selection (not for all bundle)
                 */
                $price = Mage::helper('combodeals/price')->getDiscountedPrice($selection, $option);
                
                $attributes = array(
                    'price'         => Mage::app()->getStore()->convertPrice($price),
                    'qty'           => $qty,
                    'option_id'     => $selection->getOption()->getId()
                );

                $_result = $selection->getTypeInstance(true)->prepareForCart($buyRequest, $selection);
                if (is_string($_result) && !is_array($_result)) {
                    return $_result;
                }

                if (!isset($_result[0])) {
                    return Mage::helper('checkout')->__('Cannot add item to the shopping cart.');
                }

                $result[] = $_result[0]->setParentProductId($product->getId())
                    ->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)))
                    ->addCustomOption('bundle_selection_attributes', serialize($attributes));

                if ($isStrictProcessMode) {
                    $_result[0]->setCartQty($qty);
                }

                $selectionIds[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $qty;
            }

            // "unique" key for bundle selection and add it to selections and bundle for selections
            $uniqueKey = implode('_', $uniqueKey);
            foreach ($result as $item) {
                $item->addCustomOption('bundle_identity', $uniqueKey);
            }
            $product->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)));
            $product->addCustomOption('bundle_selection_ids', serialize($selectionIds));

            return $result;
        }

        return $this->getSpecifyOptionMessage();
    }

    /**
     * Check if product can be bought
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Product_Type
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product = null)
    {
        $product            = $this->getProduct($product);
        $selectionIds       = $product->getCustomOption('bundle_selection_ids');
        $selectionIds       = (array) unserialize($selectionIds->getValue());
        $buyRequest         = $product->getCustomOption('info_buyRequest');
        $buyRequest         = new Varien_Object(unserialize($buyRequest->getValue()));
        $bundleOption       = $buyRequest->getBundleOption();

        if (empty($bundleOption) && empty($selectionIds)) {
            Mage::throwException($this->getSpecifyOptionMessage());
        }

        return $this;
    }

    /**
     * Return product weight based on weight_type attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getWeight($product = null)
    {
        if ($this->getProduct($product)->getData('weight_type')) {
            return $this->getProduct($product)->getData('weight');
        } else {
            $weight = 0;

            if ($this->getProduct($product)->hasCustomOptions()) {
                $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
                $selectionIds = unserialize($customOption->getValue());
                $selections = $this->getSelectionsByIds($selectionIds, $product);
                foreach ($selections->getItems() as $selection) {
                    $qtyOption = $this->getProduct($product)
                        ->getCustomOption('selection_qty_' . $selection->getSelectionId());
                    if ($qtyOption) {
                        $weight += $selection->getWeight() * $qtyOption->getValue();
                    } else {
                        $weight += $selection->getWeight();
                    }
                }
            }
            return $weight;
        }
    }

    /**
     * Retrieve bundle options collection based on ids
     *
     * @param array $optionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Option_Collection
     */
    public function getOptionsByIds($optionIds, $product = null)
    {
        sort($optionIds);

        $usedOptions     = $this->getProduct($product)->getData($this->_keyUsedOptions);
        $usedOptionsIds  = $this->getProduct($product)->getData($this->_keyUsedOptionsIds);

        if (!$usedOptions || serialize($usedOptionsIds) != serialize($optionIds)) {
            $usedOptions = Mage::getModel('combodeals/option')->getResourceCollection()
                ->setProductIdFilter($this->getProduct($product)->getId())                
                ->setIdFilter($optionIds);
            $this->getProduct($product)->setData($this->_keyUsedOptions, $usedOptions);
            $this->getProduct($product)->setData($this->_keyUsedOptionsIds, $optionIds);
        }
        return $usedOptions;
    }

    /**
     * Retrieve bundle selections collection based on ids
     *
     * @param array $selectionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    public function getSelectionsByIds($selectionIds, $product = null)
    {
        sort($selectionIds);

        $usedSelections     = $this->getProduct($product)->getData($this->_keyUsedSelections);
        $usedSelectionsIds  = $this->getProduct($product)->getData($this->_keyUsedSelectionsIds);

        if (!$usedSelections || serialize($usedSelectionsIds) != serialize($selectionIds)) {
            $storeId = $this->getProduct($product)->getStoreId();
            $usedSelections = Mage::getResourceModel('combodeals/selection_collection')
                ->addAttributeToSelect('*')
                ->setFlag('require_stock_items', true)
                ->setFlag('product_children', true)
                ->addStoreFilter($this->getStoreFilter($product))
                ->setStoreId($storeId)
                ->setPositionOrder()
                ->setSelectionIdsFilter($selectionIds);

            $this->getProduct($product)->setData($this->_keyUsedSelections, $usedSelections);
            $this->getProduct($product)->setData($this->_keyUsedSelectionsIds, $selectionIds);
        }
        return $usedSelections;
    }

    /**
     * Return product sku based on sku_type attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSku($product = null)
    {
        return $this->getProduct($product)->getData('sku');
    }
}