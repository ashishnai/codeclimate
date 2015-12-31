<?php
/** 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Helper_Timer extends Mage_Core_Helper_Abstract
{

 const COMBODEAL_COUNTDOWN_TIMER = 'combodeals_admin/timer_setting/enable_timer';
 
 
    /*
     * check if combodeal countdown timer is available for frontend
     * 
     * return bool 
     */
    public function isEnableModuleOutput()
    {
        if(Mage::getStoreConfig(self::COMBODEAL_COUNTDOWN_TIMER)) {
            return true;
        }
    }
 
}