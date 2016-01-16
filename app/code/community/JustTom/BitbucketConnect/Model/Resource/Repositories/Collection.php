<?php
/**
 * Created by PhpStorm.
 * User: thomasburman
 * Date: 16/01/2016
 * Time: 15:03
 */ 
class JustTom_BitbucketConnect_Model_Resource_Repositories_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('justtom_bitbucketconnect/repositories');
    }

}