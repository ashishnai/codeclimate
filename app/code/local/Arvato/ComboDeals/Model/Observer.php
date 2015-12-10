<?php

/**
 * Catalog Observer
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Shireen Nimachwala <shireenn@cybage.com>
 */
class Arvato_ComboDeals_Model_Observer {

    /**
     * Setting Comdo deal product Data to product for further processing
     *
     * @param Varien_Object $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function prepareProductSave($observer) {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            if (!$product->getSku()) {
                $product->setSku(rand(2, 4));
                $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
                $product->save();
            }
        }
        return $this;
    }

    /**
     * Disable the attributes which are not required
     *
     * event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $event
     */
    public function removeAttributes(Varien_Event_Observer $event) {
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
    public function removeTabs(Varien_Event_Observer $event) {
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
