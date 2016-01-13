<?php
/**
 * Created by PhpStorm.
 * User: thomasburman
 * Date: 08/01/2016
 * Time: 17:02
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tokensTable = $installer->getConnection()
    ->newTable($installer->getTable('justtom_bitbucketconnect/tokens'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'identity' => true,
        'nullable' => null,
        'primary' => true
    ), 'Unique Record Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => null
    ), 'Customers Entity Id')
    ->addColumn('access_token', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => null
    ), 'Customer Access Token')
    ->addColumn('refresh_token', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => null
    ), 'Customer Refresh Token')
    ->addColumn('expires_at', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => null
    ), 'Token Expiry Time')
    ->addColumn('scope', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true
    ),'Toke Access Scope')
    ->addForeignKey($installer->getFkName('justtom_bitbucketconnect/tokens', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Bitbucket oAuth Token Table');

$installer->getConnection()->createTable($tokensTable);
unset($tokensTable);

$installer->endSetup();