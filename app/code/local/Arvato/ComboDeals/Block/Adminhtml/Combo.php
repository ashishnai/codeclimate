<?php
class Arvato_ComboDeals_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout(); 
	  
    }
}<?xml version="1.0"?>
<config>
  <modules>
    <Arvato_ComboDeals>
      <version>0.1.0</version>
    </Arvato_ComboDeals>
  </modules>
  <frontend>
    <routers>
      <combodeals>
        <use>standard</use>
          <args>
            <module>Arvato_ComboDeals</module>
            <frontName>combodeals</frontName>
          </args>
      </combodeals>
    </routers>
		<layout>
		  <updates>
			<combodeals>
			  <file>combodeals.xml</file>
			</combodeals>
		  </updates>
		</layout>
  </frontend>
  <global>
	<catalog>
		<product>
			<type>
				<combodeal translate="label" module="catalog">
					<label>Combo Deal Product</label>
					<model>combodeals/product_type_comboproduct</model>
					<is_qty>1</is_qty>
					<price_model>combodeals/product_price</price_model>
					<composite>1</composite>					
				</combodeal>
			</type>
		</product>        
	</catalog>
    <helpers>
      <combodeals>
        <class>Arvato_ComboDeals_Helper</class>
      </combodeals>
    </helpers>
	<blocks>
	  <combodeals>
		<class>Arvato_ComboDeals_Block</class>
	  </combodeals>
	</blocks>
	<models>
	  <combodeals>
		<class>Arvato_ComboDeals_Model</class>
		<resourceModel>combodeals_mysql4</resourceModel>
	  </combodeals>
	  <rewrite>
	    <bundle>
		    <product_type>Arvato_ComboDeals_Model_Product_Type</product_type>
		</bundle>
	  </rewrite>
	</models>
		<resources>
		  <combodeals_setup>
			<setup>
			  <module>Arvato_ComboDeals</module>
			</setup>
			<connection>
			  <use>core_setup</use>
			</connection>
		  </combodeals_setup>
		  <combodeals_write>
			<connection>
			  <use>core_write</use>
			</connection>
		  </combodeals_write>
		  <combodeals_read>
			<connection>
			  <use>core_read</use>
			</connection>
		  </combodeals_read>
		</resources>	
	</global>
  <admin>
	<routers>
	  <combodeals>
	    <use>admin</use>
		<args>
		  <module>Arvato_ComboDeals</module>
		  <frontName>admin_combodeals</frontName>
		</args>
	  </combodeals>
	</routers>
  </admin>
  <adminhtml>
	<events>
		<catalog_product_prepare_save>
			<observers>
				<combodeal_product_save>
					<class>combodeals/observer</class>
					<method>prepareProductSave</method>
				</combodeal_product_save>
			</observers>
		</catalog_product_prepare_save>
		<core_block_abstract_prepare_layout_before>
			<observers>
				<combodeal_remove_attributes>
					<class>combodeals/observer</class>
					<method>removeAttributes</method>
				</combodeal_remove_attributes>
			</observers>
		</core_block_abstract_prepare_layout_before>
		<core_block_abstract_prepare_layout_after>
			<observers>
				<combodeal_remove_tab>
					<class>combodeals/observer</class>
					<method>removeTabs</method>
				</combodeal_remove_tab>
			</observers>
		</core_block_abstract_prepare_layout_after>
	</events>	
	<menu>
	  <combodeals module="combodeals">
		<title>ComboDeals</title>
		<sort_order>100</sort_order>
		<children>
		  <combodealsbackend module="combodeals">
			<title>Backend Page Title</title>
			<sort_order>0</sort_order>
			<action>admin_combodeals/adminhtml_combodealsbackend</action>
		  </combodealsbackend>
		</children>
	  </combodeals>
	</menu>
	<acl>
	  <resources>
		<all>
		  <title>Allow Everything</title>
		</all>
		<admin>
		  <children>
			<combodeals translate="title" module="combodeals">
			  <title>ComboDeals</title>
			  <sort_order>1000</sort_order>
			  <children>
		  <combodealsbackend translate="title">
			<title>Backend Page Title</title>
		  </combodealsbackend>
			  </children>
			</combodeals>
		  </children>
		</admin>
	  </resources>
	</acl>
	<layout>
	  <updates>
		<combodeals>
		  <file>combodeals.xml</file>
		</combodeals>
	  </updates>
	</layout>
  </adminhtml>
</config> <?xml version="1.0"?>
<config>
	  <sections>
		<combo_deal_config  translate="label" module="combodeals">                    
		<label>Combi Deal</label>
		<tab>combo_deal</tab>
		<frontend_type>text</frontend_type>
		<sort_order>0</sort_order>
		<show_in_default>1</show_in_default>
		<show_in_website>1</show_in_website>
		<show_in_store>1</show_in_store>
		</combo_deal_config>
		<combo_deal_general  translate="label" module="combodeals">            
					<groups>
				      <combo_deal_general translate="label"> 
					  <label>General Configuration</label>
					  <frontend_type>text</frontend_type>
					  <sort_order>0</sort_order>
					  <show_in_default>1</show_in_default>
					  <show_in_website>1</show_in_website>
					  <show_in_store>1</show_in_store>
				       <fields>
                          <enable_frontend translate="label">
                            <label>Enable Frontend</label>
                            <frontend_type>select</frontend_type>
                            <source_model>adminhtml/system_config_source_yesno</source_model>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </enable_frontend>
					   </fields>
					   </combo_deal_general>
					</groups>
		</combo_deal_general>
		<combo_deal_global  translate="label" module="combodeals">            
					<groups>
				      <combo_deal_global translate="label"> 
					  <label>Combi Deal </label>
					  <frontend_type>text</frontend_type>
					  <sort_order>1</sort_order>
					  <show_in_default>1</show_in_default>
					  <show_in_website>1</show_in_website>
					  <show_in_store>1</show_in_store>
				       <fields>
                          <enable_dedicated_page translate="label">
                            <label>•	Enable on dedicated page</label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </enable_dedicated_page>
                          <header_title translate="label">
                            <label>Header title</label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </header_title>
                          <number_combo_deals translate="label">
                            <label>Number of Combi deals</label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </number_combo_deals>
                          <product_sort_by translate="label">
                            <label>•	Product Detail Page Sort by</label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </product_sort_by>
                          <category_page_sort_by translate="label">
                            <label>Dedicated Page Sort by</label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </category_page_sort_by>
                          <combo_deal_title_cart translate="label">
                            <label>Promotion discount title on cart</label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </combo_deal_title_cart>
                          <combo_deal_title_category translate="label">
                            <label>Promotion discount title on </label>
							<frontend_type>text</frontend_type>
                            <sort_order>0</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                          </combo_deal_title_category>
					   </fields>
					   </combo_deal_global>
					</groups>
		</combo_deal_global>
	  </sections>
</config>
<?php
class Arvato_ComboDeals_Helper_Data extends Mage_Core_Helper_Abstract
{
}
	 <?php
/**
 * Catalog Observer
 *
 * @category   Arvato
 * @package    Arvato_ComboDeals
 * @author     Cybage Team <core@cybage.com>
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
	 * Overwrite the cache field in the product to remove disabled attributes
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
	
}<?php
class Arvato_ComboDeals_Model_Order_Creditmemo_Total_Discount 
extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

		return $this;

        $order = $creditmemo->getOrder();
        $orderDiscountTotal        = $order->getDiscountTotal();

        if ($orderDiscountTotal) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$orderDiscountTotal);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$orderDiscountTotal);
        }

        return $this;
    }
}<?php
class Arvato_ComboDeals_Model_Order_Invoice_Total_Discount
extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$order=$invoice->getOrder();
        $orderDiscountTotal = $order->getDiscountTotal();
        if ($orderDiscountTotal&&count($order->getInvoiceCollection())==0) {
            $invoice->setGrandTotal($invoice->getGrandTotal()+$orderDiscountTotal);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$orderDiscountTotal);
        }
        return $this;
    }
}<?php
class Arvato_ComboDeals_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{

}<?php
/**
 * Adminhtml product edit tabs
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @author      Shireen N <shireenn@cybage.com>
 */
class Arvato_ComboDeals_Model_Product_Type_ComboProduct extends Mage_Catalog_Model_Product_Type_Abstract
{

}<?php
/**
 * Adminhtml product edit tabs
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @author      Shireen N <shireenn@cybage.com>
 */

class Arvato_ComboDeals_Model_Product_Type extends Mage_Bundle_Model_Product_Type

{
	const TYPE_COMBODEAL = 'combodeal';
}<?php
class Arvato_ComboDeals_Model_Quote_Address_Total_Discount 
extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
     public function __construct()
    {
         $this -> setCode('discount_total');
         }
    /**
     * Collect totals information about discount
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */
     public function collect(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: collect($address);
         $items = $this->_getAddressItems($address);
         if (!count($items)) {
            return $this;
         }
         $quote= $address->getQuote();

		 //amount definition

         $discountAmount = 0.01;

         //amount definition

         $discountAmount = $quote -> getStore() -> roundPrice($discountAmount);
         $this -> _setAmount($discountAmount) -> _setBaseAmount($discountAmount);
         $address->setData('discount_total',$discountAmount);

         return $this;
     }
    
    /**
     * Add discount totals information to address object
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */
     public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: fetch($address);
         $amount = $address -> getTotalAmount($this -> getCode());
         if ($amount != 0){
             $address -> addTotal(array(
                     'code' => $this -> getCode(),
                     'title' => $this -> getLabel(),
                     'value' => $amount
                    ));
         }
        
         return $this;
     }
    
    /**
     * Get label
     * 
     * @return string 
     */
     public function getLabel()
    {
         return Mage :: helper('combodeals') -> __('Combi Deal Discount');
    }
}<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table tablename(tablename_id int not null auto_increment, name varchar(100), primary key(tablename_id));
    insert into tablename values(1,'tablename1');
    insert into tablename values(2,'tablename2');
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 