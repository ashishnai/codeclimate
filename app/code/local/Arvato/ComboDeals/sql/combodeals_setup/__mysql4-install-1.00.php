<?php

$installer = $this;

$installer->startSetup();

// create option table which contains master data
$optionTable = $installer->getConnection()
		->newTable($installer->getTable('arvato_combodeal/option'))
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
				$this->getFkName('arvato_combodeal/option', 'parent_id', 'catalog/product', 'entity_id'),
				'parent_id',
				$this->getTable('catalog/product'),
				'entity_id',
				Varien_Db_Ddl_Table::ACTION_CASCADE,
				Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('Catalog Product ComboDeal Option');


$installer->getConnection()->createTable($optionTable);

// create selection table which contains combination data
$selectionTable = $installer->getConnection()
		->newTable($installer->getTable('arvato_combodeal/selection'))
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
				$this->getFkName('arvato_combodeal/selection', 'option_id', 'arvato_combodeal/option', 'option_id'),
				'option_id',
				$this->getTable('arvato_combodeal/option'),
				'option_id',
				Varien_Db_Ddl_Table::ACTION_CASCADE,
				Varien_Db_Ddl_Table::ACTION_CASCADE)
		->addForeignKey(
				$this->getFkName('arvato_combodeal/selection', 'parent_product_id', 'catalog/product', 'entity_id'),
				'parent_product_id',
				$this->getTable('catalog/product'),
				'entity_id',
				Varien_Db_Ddl_Table::ACTION_CASCADE,
				Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('Catalog Product ComboDeal Selection');


$installer->getConnection()->createTable($selectionTable);

$installer->endSetup();


/*$installer = $this;
$installer->startSetup();


// Mage_Eav_Model_Entity_Setup
$newSetName = 'Combo Deals';
$catalogProductEntityTypeId = (int) $installer->getEntityTypeId('catalog_product');

$attributeSet = Mage::getModel('eav/entity_attribute_set')
		    ->setEntityTypeId($catalogProductEntityTypeId)
		    ->setAttributeSetName($newSetName);

if ($attributeSet->validate()) {
	$attributeSet
			->save();

}
else {
	die('Attribute set with name ' . $newSetName . ' already exists.');
}

//create an attribute group instance
$attributeGroup = Mage::getModel('eav/entity_attribute_group');
$attributeGroup->setAttributeGroupName('General')
//link to the current set
->setAttributeSetId($attributeSet->getId())
		->setSortOrder(100);
$attributeGroup->save();
// Add existing attribute to group
$skuAttributeId = $installer->getAttributeId($catalogProductEntityTypeId, 'sku');
$nameAttributeId = $installer->getAttributeId($catalogProductEntityTypeId, 'name');
$descriptionAttributeId = $installer->getAttributeId($catalogProductEntityTypeId, 'desciption');
$statusAttributeId = $installer->getAttributeId($catalogProductEntityTypeId, 'status');
$taxAttributeId = $installer->getAttributeId($catalogProductEntityTypeId, 'tax_class_id');
$installer->addAttributeToGroup($catalogProductEntityTypeId, $attributeSet->getId(), $attributeGroup->getId(), $skuAttributeId, null);
$installer->addAttributeToGroup($catalogProductEntityTypeId, $attributeSet->getId(), $attributeGroup->getId(), $nameAttributeId, null);
$installer->addAttributeToGroup($catalogProductEntityTypeId, $attributeSet->getId(), $attributeGroup->getId(), $descriptionAttributeId, null);
$installer->addAttributeToGroup($catalogProductEntityTypeId, $attributeSet->getId(), $attributeGroup->getId(), $statusAttributeId, null);
$installer->addAttributeToGroup($catalogProductEntityTypeId, $attributeSet->getId(), $attributeGroup->getId(), $taxAttributeId, null);
$installer->endSetup();

*/