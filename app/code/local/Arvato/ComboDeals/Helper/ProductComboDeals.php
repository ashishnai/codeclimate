<?php
/**
 * Contains the logic for Saving all combo deal options of a product
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */

class Arvato_ComboDeals_Helper_ProductComboDeals extends Mage_Core_Helper_Abstract
{

    /**
     * Save type related data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Arvato_ComboDeals_Model_Product_Type
     */
    public function save($product)
    {

        $resource = Mage::getResourceModel('combodeals/comboDeals');

        $options = $product->getComboDealOptionsData();
        if ($options) {

            foreach ($options as $key => $option) {

                if (isset($option['option_id']) && $option['option_id'] == '') {
                    unset($option['option_id']);
                }

                $optionModel = Mage::getModel('combodeals/option')
                    ->setData($option)
                    ->setParentId($product->getId())
                    ->setStoreId($product->getStoreId());

                $optionModel->isDeleted((bool)$option['delete']);
                $optionModel->save();

                $options[$key]['option_id'] = $optionModel->getOptionId();
            }

            $usedProductIds = array();
            $excludeSelectionIds = array();

            $selections = $product->getComboDealSelectionsData();
            if ($selections) {
                foreach ($selections as $index => $group) {
                    foreach ($group as $key => $selection) {
                        if (isset($selection['selection_id']) && $selection['selection_id'] == '') {
                            unset($selection['selection_id']);
                        }

                        $selectionModel = Mage::getModel('combodeals/selection')
                            ->setData($selection)
                            ->setOptionId($options[$index]['option_id'])
                            ->setParentProductId($product->getId());

                        $selectionModel->isDeleted((bool)$selection['delete']);
                        $selectionModel->save();

                        $selection['selection_id'] = $selectionModel->getSelectionId();

                        if ($selectionModel->getSelectionId()) {
                            $excludeSelectionIds[] = $selectionModel->getSelectionId();
                            $usedProductIds[] = $selectionModel->getProductId();
                        }

                    }
                }

                $resource->dropAllUnneededSelections($product->getId(), $excludeSelectionIds);
            }
        }

        return $product;
    }
}
