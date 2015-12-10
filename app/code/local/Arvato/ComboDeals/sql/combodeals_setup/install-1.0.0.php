<?php
/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * @author      Mayur Patel <mayurpate@cybage.com>
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

// create option table which contains master data
if (!$installer->getConnection()->isTableExists($installer->getTable('combodeals/option'))) {
    $optionTable = $installer->getConnection()
        ->newTable($installer->getTable('combodeals/option'))
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Option Id')
        ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Parent Product Id')
        ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            ), 'From Date')
        ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            ), 'To Date')    
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'unsigned' => true
            ), 'Store Id')    
        ->addForeignKey(
            $this->getFkName('combodeals/option', 'parent_id', 'catalog/product', 'entity_id'),
            'parent_id',
            $this->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Catalog Product ComboDeal Option');

    $installer->getConnection()->createTable($optionTable);
}

// create selection table which contains combination data
if (!$installer->getConnection()->isTableExists($installer->getTable('combodeals/selection'))) {
    $selectionTable = $installer->getConnection()
        ->newTable($installer->getTable('combodeals/selection'))
        ->addColumn('selection_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Selection Id')
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Option Id')
        ->addColumn('parent_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Parent Product Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Id')
        ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Position for Sorting')
        ->addColumn('discount_type', Varien_Db_Ddl_Table::TYPE_TEXT, 225, array(
            'nullable' => true
            ), 'Discount Type')
        ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false
            ), 'Discount Amount')
        ->addColumn('minimum_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'unsigned' => true
            ), 'Minimum Quantity')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'unsigned' => true
            ), 'Store Id')    
        ->addForeignKey(
            $this->getFkName('combodeals/selection', 'option_id', 'combodeals/option', 'option_id'),
            'option_id',
            $this->getTable('combodeals/option'),
            'option_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $this->getFkName('combodeals/selection', 'parent_product_id', 'catalog/product', 'entity_id'),
            'parent_product_id',
            $this->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Catalog Product ComboDeal Selection');

    $installer->getConnection()->createTable($selectionTable);
}

$installer->endSetup();