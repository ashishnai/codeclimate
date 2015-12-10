<?php
/**
 * Prepare product data to save
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Model_Observer
{
    /**
     * Setting options, selections and product Data to combo deal product for father processing
     *
     * @param Varien_Object $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if($product->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL){            
            // set visibility to "Not Visible Individually"
            $product->setVisibility('1');
            
            if (($items = $request->getPost('combodeals_options')) && !$product->getCompositeReadonly()) {
                $product->setComboDealOptionsData($items);
            }

            if (($selections = $request->getPost('combodeals_selections')) && !$product->getCompositeReadonly()) {
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
        if($block->getProduct()->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL){
            $block->getProduct()->setVisibility('1');
            $block->getProduct()->lockAttribute('visibility');
            $adminSession = Mage::getSingleton('admin/session');
        }else{
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
        if($block->getProduct()->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL){
            $block->removeTab('related'); 
            $block->removeTab('upsell'); 
            $block->removeTab('crosssell'); 
            $block->removeTab('reviews'); 
            $block->removeTab('tags'); 
            $block->removeTab('customers_tags'); 
            $block->removeTab('customer_options'); 
            // fix tab selection, as we might have removed the active tab
            $tabs = $block->getTabsIds();
            if (count($tabs) == 0) {
                $block->setActiveTab(null);
            } else {
                $block->setActiveTab($tabs[0]);
            }
        } else{

        }
    }
}