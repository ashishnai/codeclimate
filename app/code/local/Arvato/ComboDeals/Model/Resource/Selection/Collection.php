<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeal_Model_Resource_Selection_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init model and resource model
     *
     */
    protected function _construct()
    {
        $this->_init('combodeals/selection');
    }
}