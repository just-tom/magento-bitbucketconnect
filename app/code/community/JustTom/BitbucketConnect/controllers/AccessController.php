<?php

class JustTom_BitbucketConnect_AccessController
    extends Mage_Core_Controller_Front_Action
{
    public function clientAction()
    {
        $bitbucketProvider = new Stevenmaguire\OAuth2\Client\Provider\Bitbucket(
            array(
                'clientId' => Mage::getStoreConfig('justtom_bitbucketconnect/global/client_id'),
                'clientSecret' => Mage::getStoreConfig('justtom_bitbucketconnect/global/client_password'),
                'redirectUri' => Mage::getStoreConfig('justtom_bitbucketconnect/global/redirect_uri')
            )
        );
    }

    public function callbackAction()
    {

    }
}