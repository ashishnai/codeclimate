<?php


/**
 * Combodeals Product block
 *
 * @category   Combodeals
 * @package    Arvato_Combodeals
 */
class Arvato_ComboDeals_Block_Combodeals extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{
    private $_options;

    protected function _toHtml()
    {
        if ($this->_hasProductComboDeals())
        {
            return parent::_toHtml();
        }
    }

    public function getProduct()
    {
        $widgetProductId = $this->getData('product_id');

        // widget or block mode
        if (!empty($widgetProductId))
        {
            $product = Mage::getModel('catalog/product')
                ->load($widgetProductId);

            if (!$this->hasData('product'))
            {
                $this->setData('product', $product);
            }
            return $product;
        }
        else
        {
            return parent::getProduct();
        }
    }

    /**
     * Retrieve list of combodeal product options
     *
     * @return array
     */
    public function getOptions()
    {
        if (empty($this->_options))
        {
            $helper = Mage::helper("combodeals/option");
            $this->_options = $helper->getOptions($this->getProduct());
        }

        return $this->_options;
    }

    public function getComboDealAddToCartUrl($optionId)
    {
        $url = $this->getUrl('arvato_combodeal/cart/add',
            array(
                'product_id' => $this->getProduct()->getId(),
                'option_id'  => $optionId
            ));

        return $url;
    }

    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     *
     * @return string
     */
    public function getDealPriceHtml($product, $option, $displayMinimalPrice = false, $idSuffix = '')
    {
        $block = $this->getLayout()->createBlock('arvato_combodeal/price')
            ->setProduct($product)
            ->setOption($option)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix);

        return $block->toHtml();
    }

    /*
     * Calculates the total minimum product price without the combo deal
     */
    public function getTotalPrice($option)
    {
        /* @var $priceHelper Arvato_ComboDeal_Helper_Price */
        $priceHelper = Mage::helper('combodeals/price');
        $product = $this->getProduct();
        $inclTax = $priceHelper->displayIncludingTax();

        $total = $priceHelper->getRegularProductPrice($product, $inclTax);

        $selections = $option->getSelections();

        if (!empty($selections)){
            foreach ($selections as $selection)
            {
                $total += $priceHelper->getRegularProductPrice($selection, $inclTax);
            }
        }

        return $total;
    }

    /*
     * Calculates the total discounted price (when the deal gets applied)
     */
    public function getTotalDiscountedPrice($option)
    {
        /* @var $priceHelper Arvato_ComboDeal_Helper_Price */
        $priceHelper = Mage::helper('arvato_combodeal/price');
        $product = $this->getProduct();
        $inclTax = $priceHelper->displayIncludingTax();

        $productPrice = $priceHelper->getRegularProductPrice($product, $inclTax);
        $total = $priceHelper->getDiscountedPrice($product, $option, $productPrice);

        $selections = $option->getSelections();

        if (!empty($selections))
        {
            foreach ($selections as $selection)
            {
                $productPrice = $priceHelper->getRegularProductPrice($selection, $inclTax);
                $total += $priceHelper->getDiscountedPrice($selection, $option, $productPrice);
            }
        }

        return $total;
    }

    /*
     * check if the product have combo deals at all
     * 
     * @return bool
     */
    private function _hasProductComboDeals()
    {
        $options = $this->getOptions();

        return !empty($options) && count($options) > 0;
    }

    /*
    * adds the item to the array if it does not yet exist in the array
    */
    private function _addToArrayIfNotExists($array, $newItem)
    {
        if (!in_array($newItem, $array))
        {
            array_push($array, $newItem);
        }

        return $array;
    }

    /*
     * replace the last occurence of sth in a string
     */
    private function _replaceLast($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Creates a proper discount string for the option
     *
     * @param $option
     * @param $amount
     *
     * @return string
     */
    private function _determineDiscountString($option, $amount)
    {
        $discountType = $option->getDiscountType();

        // what will we get?
        if ($discountType == Arvato_ComboDeal_Model_Option::DISCOUNT_TYPE_PERCENT)
        {
            $discountString = round($amount, 2) . ' %';
            return $discountString;
        }
        else
        {
            $_coreHelper = $this->helper('core');
            $discountString = $_coreHelper->currency($amount, true, false);
            return $discountString;
        }
    }

    /**
     * Prepare collection with new products
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * creates and returns a stdClass with the relevant condition and action products
     *
     * @param $option
     */
    private function _determineRelevantProductNames($option, $product)
    {
        $productNames = new stdClass();

        $productNames->actionProducts = array();
        $productNames->conditionProducts = array();
        $productNames->actionProductsInCondition = array();

        array_push($productNames->conditionProducts, Mage::helper('core')->escapeHtml($product->getName()));

        if ($option->getSelections())
        {

            foreach ($option->getSelections() as $selection)
            {
                $selectionModel = Mage::getModel('arvato_combodeal/selection')
                    ->setData($selection->getData());

                if ($selectionModel->isConditionProduct())
                {
                    $productName = Mage::helper('core')->escapeHtml($selection->getName());
                    $productNames->conditionProducts = $this->_addToArrayIfNotExists($productNames->conditionProducts, $productName);
                }

                if ($selectionModel->isActionProduct())
                {

                    $productName = Mage::helper('core')->escapeHtml($selection->getName());
                    $productNames->actionProducts = $this->_addToArrayIfNotExists($productNames->actionProducts, $productName);

                    //mark this product for later appending 'another'
                    if (in_array($productName, $productNames->conditionProducts))
                    {
                        $productNames->actionProductsInCondition = $this->_addToArrayIfNotExists($productNames->actionProductsInCondition, $productName);
                    }
                }
            }
        }

        return $productNames;
    }
}
