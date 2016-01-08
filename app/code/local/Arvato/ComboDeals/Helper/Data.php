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

    /**
     * Enable Combodeal Dedicated/Listing Page
     */
    const COMBODEAL_ENABLE_DEDICATED_PAGE = 'combodeals_admin/combodeals_setting/enable_dedicated';

    /**
     * Combdeal Block header title
     */
    const COMBODEAL_PRODUCT_HEADER_TITLE = 'combodeals_admin/combodeals_setting/header';

     /**
     * Limit of Combdeal Products
     */
    const COMBODEAL_PRODUCT_LIMIT = 'combodeals_admin/combodeals_setting/number_of_combodeals';


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
     * @return bool 
     */
    public function isEnableModuleOutput()
    {
        if(Mage::getStoreConfig(self::COMBODEAL_ENABLE_MODULE_OUTPUT))
        {
            return true;
        }
    }

     /*
     * Check whether the dedicated page display is enabled in the global system configuration
     * 
     * @return bool 
     */
    public function isEnableDedicatedPage()
    {
        if(Mage::getStoreConfig(self::COMBODEAL_ENABLE_DEDICATED_PAGE))
        {
            return true;
        }
    }

    /*
     * Get The header title for combo deal products block
     * 
     * @return string 
     */
    public function getHeaderTitle()
    {
        return Mage::getStoreConfig(self::COMBODEAL_PRODUCT_HEADER_TITLE);
    }

    /*
     * Get The Limit for displaying combo deal on product detail
     * 
     * @return int 
     */
    public function getProductLimit()
    {
        return Mage::getStoreConfig(self::COMBODEAL_PRODUCT_LIMIT);
    }

    /*
     * Check if combodeals cms page is active
     * 
     * @return bool
     */
    public function isActiveDealCms()
    {
        if ($this->isEnableDedicatedPage()) {
            $cmsCollections = Mage::getModel('cms/page')->getCollection();
            $attribute = "identifier";
            $value = "combo_deals";
            $cmsCollections->addFieldToFilter($attribute, $value);
            $item = $cmsCollections->getFirstItem();
            if($item->getData('is_active') == 1){
                return true;
            }
        }
    }
    
    
    /*
     * Get Combo deals add to cart Url
     * 
     * @param int $productId
     * @param int $optionId
     * @param array $selectionIds
     * @return string $url
     */
    public function getComboDealAddToCartUrl($productId, $optionId, $selectionIds)
    {
        $url = Mage::getUrl('combodeals/cart/add',
            array(
                'product' => $productId,
                'option_id'  => $optionId,
                'selection_id'  => implode(',', $selectionIds),
                'qty'  => 1,
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode(Mage::helper('core/url')->getCurrentUrl()),
                Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()

            ));

        return $url;
    }
    
        /*
     * Get the combo deal product stock status
     * 
     * @param Arvato_ComboDeals_Model_Option $option
     * @return  
     */
    public function getDealStockStatus($option)
    {
        $selections = $option->getSelections();
        $in_stock = array();
        if (!empty($selections)) {
            $in_stock = array();
            foreach ($selections as $selection) {
                if ($selection->getIsSalable() && Mage::helper('uandi_arvato')->isInStock($selection)) {
                    if (Mage::helper('uandi_arvato')->isFewLeft($selection)) {
                        $in_stock[] = 'few_left';
                    } else {
                        $in_stock[] = 'in_stock';
                    }
                } else {
                    $in_stock[] = 'out_of_stock';
                }
            }
        }
        return $in_stock;
    }

}