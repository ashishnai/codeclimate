<?php

/**
 *  Model for disocunt type options
 * 
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Shireen Nimachwala <shireenn@cybage.com>
 *
 */
class Arvato_ComboDeals_Model_Product_Discount extends Mage_Core_Model_Abstract
{
    const TYPE_FIXED   = 'fixed';
    const TYPE_PERCENT = 'percent';
    const TYPE_FREE    = 'free';
    const TYPE_NONE    = 'none';
    
  /**
   * Retrieve discount type option array
   *
   * @return array
   */
  static public function getOptionArray()
  {
      return array(
          self::TYPE_FIXED    => Mage::helper('catalog')->__('Fixed'),
          self::TYPE_PERCENT  => Mage::helper('catalog')->__('Percent Discount'),
          self::TYPE_FREE   => Mage::helper('catalog')->__('Free'),
          self::TYPE_NONE   => Mage::helper('catalog')->__('None'),
      );
  }
} 