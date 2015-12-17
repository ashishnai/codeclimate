<?php

/**
 * Setting options, selections and product Data to combo deal product for futher processing
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_Observer {

    /**
     * Setting Comdo deal product Data to product for further processing
     *
     * @param Varien_Object $observer
     * @return Arvato_ComboDeals_Model_Observer
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
                $product->setSku(Mage::helper("combodeals")->getJoinedSku($selections));
            }
        }
        return $observer;
    }

    /**
     * Save the combo deal options when the product is saved
     *
     * @param Varien_Event_Observer $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function afterProductSave($observer)
    {
        $typeInstance = Mage::helper("combodeals/saveComboDeals");
        $product = $observer->getEvent()->getProduct();
        $typeInstance->save($product);
    }
        
    /**
     * Disable the attributes which are not required
     *
     * event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $event
     */
    public function removeAttributes(Varien_Event_Observer $event) 
    {
        $block = $event->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            return;
        }
        if ($block->getProduct()->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            $block->getProduct()->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
            $block->getProduct()->lockAttribute('visibility');
            $block->getProduct()->lockAttribute('sku');
        } else {
            return;
        }
    }

    /**
     * Remove hidden tabs from product edit
     * event: core_block_abstract_prepare_layout_after
     *
     * @param Varien_Event_Observer $event
     */
    public function removeTabs(Varien_Event_Observer $event) 
    {
        $block = $event->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            return;
        }
        if ($block->getProduct()->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            $removeTabs = array('related', 'upsell', 'crosssell', 'reviews', 'tags', 'customers_tags', 'customer_options');
            foreach($removeTabs as $removeTab){
                $block->removeTab($removeTab);
            }
            // fix tab selection, as we might have removed the active tab
            $tabs = $block->getTabsIds();
            if (count($tabs) == 0) {
                $block->setActiveTab(null);
            } else {
                $block->setActiveTab($tabs[0]);
            }
        } else {
            return;
        }
    }
}