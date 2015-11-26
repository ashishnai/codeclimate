<?php
class Arvato_ComboDeals_Model_Order_Creditmemo_Total_Discount 
extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

		return $this;

        $order = $creditmemo->getOrder();
        $orderDiscountTotal        = $order->getDiscountTotal();

        if ($orderDiscountTotal) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$orderDiscountTotal);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$orderDiscountTotal);
        }

        return $this;
    }
}