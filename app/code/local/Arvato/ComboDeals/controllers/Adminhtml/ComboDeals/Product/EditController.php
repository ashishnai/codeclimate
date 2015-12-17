<?php

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Adminhtml_ComboDeals_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Initialize used model
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Arvato_ComboDeals');
    }

    /**
     * Product combodeal tab form action
     * 
     * @return string
     */
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