<?php
/**
 * Combodeal Order item render block
 *
 * @category   Combodeals
 * @package    Arvato_Combodeals
 * @copyright   Copyright (c) arvato 2015
 */
class Arvato_ComboDeals_Block_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
    public function isShipmentSeparately($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }
            if ($parentItem = $item->getParentItem()) {
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                if ($options = $item->getProductOptions()) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['shipment_type'])
                && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
            ) {
                return true;
            }
        }
        return false;
    }

    public function isChildCalculated($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }
            if ($parentItem = $item->getParentItem()) {
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                if ($options = $item->getProductOptions()) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['product_calculations'])
                && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
            ) {
                return true;
            }
        }
        return false;
    }

    public function getSelectionAttributes($item) {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
        return null;
    }

    public function getValueHtml($item)
    {
        if ($attributes = $this->getSelectionAttributes($item)) {
            return sprintf('%d', $attributes['qty']) . ' x ' .
                $this->escapeHtml($item->getName()) .
                " " . $this->getOrder()->formatPrice($attributes['price']);
        } else {
            return $this->escapeHtml($item->getName());
        }
    }

    /**
     * Getting all available childs for Invoice, Shipmen or Creditmemo item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getChilds($item)
    {
        $_itemsArray = array();

        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $_items = $item->getInvoice()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $_items = $item->getShipment()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $_items = $item->getCreditmemo()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                if ($parentItem = $_item->getOrderItem()->getParentItem()) {
                    $_itemsArray[$parentItem->getId()][$_item->getOrderItemId()] = $_item;
                } else {
                    $_itemsArray[$_item->getOrderItem()->getId()][$_item->getOrderItemId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getOrderItem()->getId()])) {
            return $_itemsArray[$item->getOrderItem()->getId()];
        } else {
            return null;
        }
    }

    public function canShowPriceInfo($item)
    {
        if (($item->getOrderItem()->getParentItem() && $this->isChildCalculated())
                || (!$item->getOrderItem()->getParentItem() && !$this->isChildCalculated())) {
            return true;
        }
        return false;
    }
}
