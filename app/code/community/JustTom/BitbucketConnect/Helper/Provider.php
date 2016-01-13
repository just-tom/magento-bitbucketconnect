<?php

/**
 * Created by PhpStorm.
 * User: thomasburman
 * Date: 13/01/2016
 * Time: 07:01
 */
class JustTom_BitbucketConnect_Helper_Provider
    extends Mage_Core_Helper_Abstract
{
    const XPATH_CONFIG_CLIENT_ID = 'justtom_bitbucketconnect/global/client_id';
    const XPATH_CONFIG_CLIENT_PASSWORD = 'justtom_bitbucketconnect/global/client_password';
    const XPATH_CONFIG_REDIRECT_URI = 'justtom_bitbucketconnect/global/redirect_uri';

    public function getProvider()
    {
        return new Stevenmaguire\OAuth2\Client\Provider\Bitbucket(
            array(
                'clientId' => Mage::getStoreConfig(
                    self::XPATH_CONFIG_CLIENT_ID
                ),
                'clientSecret' => Mage::getStoreConfig(
                    self::XPATH_CONFIG_CLIENT_PASSWORD
                ),
                'redirectUri' => Mage::getStoreConfig(
                    self::XPATH_CONFIG_REDIRECT_URI
                ),
            )
        );
    }
}