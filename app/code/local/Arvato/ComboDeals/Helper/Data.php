<?php
/** 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Validation class name
     */
    const VALIDATION_CLASS_HAS_PRODUCTS = 'validate-has-products';

    /**
     * Validation class name
     */
    const VALIDATION_CLASS_HAS_TWO_PRODUCTS = 'validate-has-two-products';

    /**
     * SKU prefix for combo deal product
     */
    const COMBODEAL_PRODUCT_SKU_PREFIX = 'COMBO';

    /**
     * Separator to join child SKU's to make parent SKU of combo deal product
     */
    const COMBODEAL_PRODUCT_SKU_SEPARATOR = '--';

    /**
     * New attribute set name
     */
    const COMBODEAL_ATTRIBUTE_SET_NAME = 'Combo Deals';

    /**
     * Retrieve mixed string SKU using selected child products
     *     
     * @param array $selections
     *
     * @return string
     */
    public function getJoinedSku($selections)
    {
        foreach($selections as $products) {
            $sku = $this->prepareSku($products);
            break;
        }

        // concate SKU prefix + random number + prepared SKU string
        $sku = self::COMBODEAL_PRODUCT_SKU_PREFIX . rand(10001,99999) . $sku;

        return $sku;
    }

    /**
     * Check for deleted selection and prepare SKU string
     *     
     * @param array $products
     *
     * @return string
     */
    public function prepareSku($products)
    {
        $sku = '';

        foreach($products as $product) {
            // check if selected child product is deleted
            if($product['delete']) {
                continue;
            }

            $sku .= self::COMBODEAL_PRODUCT_SKU_SEPARATOR . $product['sku'];
        }

        return $sku;
    }
}