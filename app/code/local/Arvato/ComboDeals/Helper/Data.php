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
     * Enable Combodeal system configuration
     */
    const COMBODEAL_ENABLE_MODULE_OUTPUT = 'combodeals_admin/setting/enable_frontend';
    
   // const COMBODEAL_PRODUCT_HEADER_TITLE = 'combodeals_admin/combodeals_setting/header';
    /**
     * Combdeal Block header title
     */
    const COMBODEAL_PRODUCT_HEADER_TITLE = 'combodeals_admin/combodeals_setting/header';


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
    
     /*
     * Check whether the module output is enabled in the global system configuration
     * 
     * return bool 
     */
    public function isEnableModuleOutput()
    {
        if(Mage::getStoreConfig(self::COMBODEAL_ENABLE_MODULE_OUTPUT))
        {
            return true;
        }
    }
    
    /*
     * Get The header title or combo deal products block
     * 
     * return string 
     */
    public function getHeaderTitle()
    {
        return Mage::getStoreConfig(self::COMBODEAL_PRODUCT_HEADER_TITLE);
    }
}