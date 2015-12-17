<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_Option extends Mage_Core_Model_Abstract
{
    /**
     * Default selection object
     *
     * @var Arvato_ComboDeals_Model_Selection
     */
    protected $_defaultSelection = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('combodeals/option');
        parent::_construct();
    }

    /**
     * Add selection to option
     *
     * @param Arvato_ComboDeals_Model_Selection $selection
     * @return Arvato_ComboDeals_Model_Option
     */
    public function addSelection($selection)
    {
        if (!$selection) {
            return false;
        }
        if (!$selections = $this->getData('selections')) {
            $selections = array();
        }
        array_push($selections, $selection);
        $this->setSelections($selections);
        return $this;
    }

    /**
     * Return selection by it's id
     *
     * @param int $selectionId
     * @return Arvato_ComboDeals_Model_Selection
     */
    public function getSelectionById($selectionId)
    {
        $selections = $this->getSelections();
        $i = count($selections);

        while ($i-- && $selections[$i]->getSelectionId() != $selectionId);

        return $i == -1 ? false : $selections[$i];
    }

    /*
     * Returns all selections fo this option
     * 
     * @return Arvato_ComboDeals_Model_Selection
     */
    public function getAllSelections()
    {
        return $this->getSelections();
    }

    /*
     * Returns the minimum quantity for the condition products
     * 
     * @return int
     */
    public function getMinimumQuantity()
    {
        return $this->getData('minimum_qty');
    }

    /*
     * gets the discount amount for the action products
     * 
     * @return decimal
     */
    public function getAmount()
    {
        return $this->getData('discount_amount');
    }

    /*
     * gets the type of discount (percent or fixed or free or none)
     * 
     * @return string
     */
    public function getDiscountType()
    {
        return $this->getData('discount_type');
    }
}