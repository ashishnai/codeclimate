<?php

class Arvato_ComboDeals_Adminhtml_ComboDeals_SelectionController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('arvato_combodeals');

    }

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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }
}
