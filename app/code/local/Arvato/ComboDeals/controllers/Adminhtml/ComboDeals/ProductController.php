<?php

/**
 * Controller for grid actions
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Adminhtml_ComboDeals_ProductController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('catalog/combodeals')
                ->_title($this->__('Catalog'))->_title($this->__('Combo Deal Products'))
                ->_addBreadcrumb($this->__('Combo Deal Products'), $this->__('Combo Deal Products'));
        return $this;
    }

    /**
     * Get combodeals grid block
     *
     */
    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Combo Deals grid
     */
    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals_grid')->toHtml()
        );
    }

    /**
     * Delete combo deal products
     */
    public function massDeleteAction() {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                            $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Update product(s) status action
     *
     */
    public function massStatusAction() {
        $productIds = (array) $this->getRequest()->getParam('product');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $status = (int) $this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                    ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                    ->addException($e, $this->__('An error occurred while updating the product(s) status.'));
        }

        $this->_redirect('*/*/', array('store' => $storeId));
    }

    /**
     * Validate batch of products before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $productIds
     * @param  int $status
     * @return void
     */
    public function _validateMassStatus(array $productIds, $status) {
        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
                throw new Mage_Core_Exception(
                $this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.')
                );
            }
        }
    }

    /**
     * Export combo deal product grid to CSV format
     */
    public function exportCsvAction() {
        $fileName = 'arvato_combodeals.csv';
        $grid = $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportXmlAction() {
        $fileName = 'arvato_combodeals.xml';
        $grid = $this->getLayout()->createBlock('combodeals/adminhtml_comboDeals');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

}
