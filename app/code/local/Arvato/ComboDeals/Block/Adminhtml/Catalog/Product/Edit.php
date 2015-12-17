<?php
/**
 * Override Catalog Edit Form
 *
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Shireen Nimachwala <shireenn@cybage.com>
 * 
 */
class Arvato_ComboDeals_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit
{
    /*
     * override the method for changing the back url based on combodeal refferer
     * 
     */
     protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pos = strpos(Mage::app()->getRequest()->getServer('HTTP_REFERER'), 'comboDeals_product');
        if($pos) {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Back'),
                        'onclick'   => 'setLocation(\''
                            . $this->getUrl('adminhtml/comboDeals_product/index').'\')',
                        'class' => 'back'
                    ))
            );
            
        }
        else if (!$this->getRequest()->getParam('popup')) {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Back'),
                        'onclick'   => 'setLocation(\''
                            . $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                        'class' => 'back'
                    ))
            );
        } else {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Close Window'),
                        'onclick'   => 'window.close()',
                        'class' => 'cancel'
                    ))
            );
        }
        return $this;
    }
}
