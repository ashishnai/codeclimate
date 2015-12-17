<?php

/**
 * Catalog product save and redirect to Combodeal Grid 
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Model_SaveRedirectObserver
{

    /**
     * Redirect Back to Combo deal grid if the new param exist
     *
     * event : controller_action_postdispatch_adminhtml_catalog_product_save
     * @param Varien_Event_Observer $observer
     * @return Arvato_ComboDeals_Model_SaveRedirectObserver
     */
    public function saveAndRedirect($observer)
    {
        if (Mage::app()->getRequest()->getParam('redirectBack')) {
            $controllerAction = $observer->getEvent()->getControllerAction();
            $observer->getEvent()->getControllerAction()->getResponse();
            $controllerAction->getResponse()->setRedirect($controllerAction->getUrl('adminhtml/comboDeals_product/'));
            return;
        }
    }

}
