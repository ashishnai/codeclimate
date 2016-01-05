<?php
/**
 * Shopping cart item render block
 *
 * @category   Combodeals
 * @package    Arvato_Combodeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Checkout_Cart_Item_Renderer extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer
{
    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    protected function _getBundleOptions($useCache = true)
    {
        return Mage::helper('combodeals/catalog_product_configuration')->getBundleOptions($this->getItem());
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @return decimal
     */
    protected function _getSelectionFinalPrice($selectionProduct)
    {
        $helper = Mage::helper('combodeals/catalog_product_configuration');
        $result = $helper->getSelectionFinalPrice($this->getItem(), $selectionProduct);
        return $result;
    }

    /**
     * Get selection quantity
     *
     * @param int $selectionId
     * @return decimal
     */
    protected function _getSelectionQty($selectionId)
    {
        return Mage::helper('combodeals/catalog_product_configuration')->getSelectionQty($this->getProduct(), $selectionId);
    }

    /**
     * Overloaded method for getting list of bundle options
     * Caches result in quote item, because it can be used in cart 'recent view' and on same page in cart checkout
     *
     * @return array
     */
    public function getOptionList()
    {
        return Mage::helper('combodeals/catalog_product_configuration')->getOptions($this->getItem());
    }

    /**
     * Return cart item error messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        $quoteItem = $this->getItem();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array(
                    'text' => $message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }

        return $messages;
    }
}
