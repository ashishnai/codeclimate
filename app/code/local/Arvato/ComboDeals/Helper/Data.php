<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Helper_Data extends Mage_Core_Helper_Abstract
{
    const VALIDATION_CLASS_HAS_PRODUCTS = 'validate-has-products';
    
    /**
     * SKU prefix for combo deal product
     */
    const COMBODEAL_PRODUCT_SKU_PREFIX = 'combo';
    
    /**
     * Separator to join child SKU's to make parent SKU of combo deal product
     */
    const COMBODEAL_PRODUCT_SKU_SEPARATOR = '--';
    
    /**
     * Retrieve mixed string SKU using selected child products
     *     
     * @param array $selections
     *
     * @return string
     */
    public function getJoinedSku($selections)
    {
        $sku = '';
        foreach($selections as $products) {
            foreach($products as $product) {
                if(!$product['delete']) {
                    $sku .= self::COMBODEAL_PRODUCT_SKU_SEPARATOR . $product['sku'];
                }
            }
        }
        $sku = self::COMBODEAL_PRODUCT_SKU_PREFIX . $sku;
        return $sku;
    }
}
