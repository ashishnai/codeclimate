<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 */

class Arvato_ComboDeals_Model_Resource_ComboDeals extends Mage_CatalogIndex_Model_Resource_Data_Abstract
{
    /**
     * Removes specified selections by ids for specified product id
     *
     * @param int $productId
     * @param array $ids
     */
    public function dropAllUnneededSelections($productId, $ids)
    {
        $where = array(
            'product_id = ?' => $productId
        );
        if (!empty($ids)) {
            $where['selection_id NOT IN (?) '] = $ids;
        }
        $this->_getWriteAdapter()
            ->delete($this->getTable('combodeals/selection'), $where);
    }


}
