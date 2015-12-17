<?php

/**
 * Setting options, selections and product Data to combo deal product for futher processing
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_Observer
{
    /**
     * Redirect Back to Combo deal grid if the new param exist
     *
     * event : controller_action_postdispatch_adminhtml_catalog_product_save
     * @param Varien_Event_Observer $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function saveAndRedirect($observer)
    {
        if (Mage::app()->getRequest()->getParam('redirectBack')) {
            $controllerAction = $observer->getEvent()->getControllerAction();
            $observer->getEvent()->getControllerAction()->getResponse();
            $controllerAction->getResponse()->setRedirect($controllerAction->getUrl('adminhtml/comboDeals_product/'));
            return;
        }
    }

    /**
     * Disable the attributes which are not required
     *
     * event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function removeAttributes($observer)
    {
        $block = $observer->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            return;
        }
        if ($block->getProduct()->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            $block->getProduct()->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
            $block->getProduct()->lockAttribute('visibility');
            $block->getProduct()->lockAttribute('sku');
        }
    }

    /**
     * Remove hidden tabs from product edit
     * event: core_block_abstract_prepare_layout_after
     *
     * @param Varien_Event_Observer $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function removeTabs($observer)
    {
        $block = $observer->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            return;
        }
        if ($block->getProduct()->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL) {
            $removeTabs = array('related', 'upsell', 'crosssell', 'reviews', 'tags', 'customers_tags', 'customer_options');
            foreach ($removeTabs as $removeTab) {
                $block->removeTab($removeTab);
            }
            // fix tab selection, as we might have removed the active tab
            $tabs = $block->getTabsIds();
            if (count($tabs) == 0) {
                $block->setActiveTab(null);
            } else {
                $block->setActiveTab($tabs[0]);
            }
        }
    }
}