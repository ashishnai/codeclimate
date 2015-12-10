<?php
/**
 * Controller for grid actions
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Shireen Nimachwala <shireenn@cybage.com>
 * 
 */

class Arvato_ComboDeals_Adminhtml_ComboDeals_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get combodeals grid block
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Combo Deal Products'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/combodeals');
        $this->_addContent($this->getLayout()->createBlock('combodeals/adminhtml_comboDeals'));
        $this->renderLayout();
    }


    /**
     * Combo Deals grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals')->toHtml()
        );
    }

    /**
     * Delete combo deal products
     */
    public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('products');
        foreach($productIds as $productId){
           $parentProductIds[] = Mage::getModel('combodeals/option')->load($productId)->getParentId();
        }
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($parentProductIds)) {
                try {
                    foreach ($parentProductIds as $parentProductId) {
                        $product = Mage::getSingleton('catalog/product')->load($parentProductId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($parentProductIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Export combo deal product grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'arvato_combodeals.csv';
        $grid = $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportXmlAction()
    {
        $fileName = 'arvato_combodeals.xml';
        $grid = $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

}