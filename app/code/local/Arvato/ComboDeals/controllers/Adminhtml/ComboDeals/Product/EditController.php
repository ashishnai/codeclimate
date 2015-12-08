<?php

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Arvato_ComboDeals_Adminhtml_ComboDeals_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{
    protected function _construct()
    {
        $this->setUsedModuleName('arvato_combodeals');
    }

    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('combodeals/adminhtml_tab', 'admin.combodeals')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }
}