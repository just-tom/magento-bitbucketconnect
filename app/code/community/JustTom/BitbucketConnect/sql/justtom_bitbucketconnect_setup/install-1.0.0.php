<?php

/**
 * Cranbri Web Solutions (c) 2016 JustTom
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM
 * , OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tokensTable = $installer->getConnection()
    ->newTable($installer->getTable('justtom_bitbucketconnect/tokens'))
    ->addColumn(
        'entity_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'identity' => true,
        'nullable' => null,
        'primary'  => true,
    ), 'Unique Record Id'
    )
    ->addColumn(
        'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => null,
    ), 'Customers Entity Id'
    )
    ->addColumn(
        'access_token', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => null,
    ), 'Customer Access Token'
    )
    ->addColumn(
        'refresh_token', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => null,
    ), 'Customer Refresh Token'
    )
    ->addColumn(
        'expires_at', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => null,
    ), 'Token Expiry Time'
    )
    ->addColumn(
        'scope', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Toke Access Scope'
    )
    ->setComment('Bitbucket oAuth Token Table');

$installer->getConnection()->createTable($tokensTable);
unset($tokensTable);

$installer->endSetup();