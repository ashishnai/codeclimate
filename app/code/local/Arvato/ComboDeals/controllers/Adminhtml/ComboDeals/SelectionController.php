<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */
class Arvato_ComboDeals_Adminhtml_ComboDeals_SelectionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize used model
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('arvato_combodeals');
    }

    /**
     * Product grid search action
     * 
     * @return string
     */
    public function searchAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('combodeals/adminhtml_comboDeals_option_search')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
           );
    }

    /**
     * Product grid action
     * 
     * @return string
     */
    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('combodeals/adminhtml_comboDeals_option_search_grid',
                    'adminhtml.combodeals.option.search.grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }
}