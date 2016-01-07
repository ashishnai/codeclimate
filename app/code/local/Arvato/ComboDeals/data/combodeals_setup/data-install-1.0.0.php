<?php

/**
 * @category    Arvato
 * @package     Arvato_ComboDeals
 * @copyright   Copyright (c) arvato 2015
 * 
 */

/* Create a new Attribute Set from Default */
// Mage_Eav_Model_Entity_Setup
$catalogProductEntityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();

$attributeSetModel = Mage::getModel('eav/entity_attribute_set')
        ->getCollection()
        ->setEntityTypeFilter($catalogProductEntityTypeId)
        ->addFieldToFilter('attribute_set_name', Arvato_ComboDeals_Helper_Data::COMBODEAL_ATTRIBUTE_SET_NAME)
        ->getFirstItem();
if (empty($attributeSetModel->getdata())) {
    $attributeSetModel = Mage::getModel('eav/entity_attribute_set');

    $attributeSetModel->setEntityTypeId($catalogProductEntityTypeId)
            ->setAttributeSetName(Arvato_ComboDeals_Helper_Data::COMBODEAL_ATTRIBUTE_SET_NAME);

    if ($attributeSetModel->validate()) {
        $attributeSetModel->save()
                ->initFromSkeleton($catalogProductEntityTypeId)
                ->save();
    }
}
/* Create CMS Page for Combo Deals */
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$cmsPage = array(
    'title' => 'Combo Deals',
    'identifier' => 'combo_deals',
    'content' => '{{block type="core/template" name="combodeals.list" template="combodeals/container.phtml"}}',
    'layout_update_xml' => '<reference name="head">
    <action method="addJs">
      <script>arvato/combodeals/timer.js</script>
    </action>
    <action method="addCss">
        <stylesheet>combodeals/combodeals.css</stylesheet>
    </action>
    </reference>',
    'is_active' => 1,
    'sort_order' => 0,
    'stores' => array(0),
    'root_template' => 'one_column'
);

Mage::getModel('cms/page')->setData($cmsPage)->save();
