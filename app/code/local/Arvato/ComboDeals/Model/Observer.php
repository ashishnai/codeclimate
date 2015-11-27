<?php
/**
 * Catalog Observer
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_Observer
{
	 /**
     * Setting Comdo deal product Data to product for father processing
     *
     * @param Varien_Object $observer
     * @return Arvato_ComboDeals_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();
		if($product->getTypeId() == Arvato_ComboDeals_Model_Product_Type::TYPE_COMBODEAL){
			$product->setSku(rand(5, 15));
			$product->setVisibility('1');
			$product->save();
		}
       // Mage::log($product->getData(), true, null);
        return $this;
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