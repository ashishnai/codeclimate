<?php
/**
 * Combo Deal Product price block
 *
 * @category   Arvato
 * @package    Arvato_Combodeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Timer extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @var Arvato_ComboDeal_Helper_Timer
     */
    protected $_timerHelper;
    /**
     * @var Mage_Core_Helper_Data
     */

    public function __construct()
    {
        parent::__construct();

        $this->_timerHelper = Mage::helper('combodeals/timer');
        $this->setSkipGenerateContent(true);
        $this->setTemplate('combodeals/timer.phtml');
    }
}    