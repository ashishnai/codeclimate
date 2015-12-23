<?php

/**
 * Setting options, selections and product Data to combo deal product for futher processing
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_ProductSaveObserver
{
    /**
     * Setting Comdo deal product Data to product for further processing
     *
     * @param Varien_Object $observer
     * @return Arvato_ComboDeals_Model_ProductSaveObserver
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if ($product->getCompositeReadonly()) {
            return $observer;
        }

        if ($product->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL){
            // set visibility to "Not Visible Individually"
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);

            if (($items = $request->getPost('combodeals_options'))) {
                $product->setComboDealOptionsData($items);
            }

            if (($selections = $request->getPost('combodeals_selections'))) {
                $product->setComboDealSelectionsData($selections);

                // set SKU using mixed SKU string of selected child products
                if($product->getSku() == NULL) {
                    $product->setSku(Mage::helper("combodeals")->getJoinedSku($selections));
                }
            }
        }
        return $observer;
    }

    /**
     * Save the combo deal options when the product is saved
     *
     * @param Varien_Event_Observer $observer
     * @return Arvato_ComboDeals_Model_ProductSaveObserver
     */
    public function afterProductSave($observer)
    {
        $typeInstance = Mage::helper("combodeals/saveComboDeals");
        $product = $observer->getEvent()->getProduct();
        $typeInstance->save($product);
    }
}