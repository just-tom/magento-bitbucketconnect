<?php

/**
 * Created by PhpStorm.
 * User: thomasburman
 * Date: 13/01/2016
 * Time: 06:34
 */
class JustTom_BitbucketConnect_Model_Resource_Tokens
    extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('justtom_bitbucketconnect/tokens', 'entity_id');
    }

}