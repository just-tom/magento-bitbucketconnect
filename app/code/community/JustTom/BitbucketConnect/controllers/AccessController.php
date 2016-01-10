<?php

class JustTom_BitbucketConnect_AccessController
    extends Mage_Core_Controller_Front_Action
{
    public function clientAction()
    {
        $test = new League\OAuth2\Client\Provider\GenericProvider();
        $bitbucketProvider = new Stevenmaguire\OAuth2\Client\Provider\Bitbucket(
            array(
                'clientId' => Mage::getStoreConfig('justtom_bitbucketconnect/global/client_id'),
                'clientSecret' => Mage::getStoreConfig('justtom_bitbucketconnect/global/client_password'),
                'redirectUri' => 'http://aff75f77.ngrok.io/index.php/bitbucket/access/callback'
            )
        );

        $dave = '';
    }

    public function callbackAction()
    {
        $dave = '';
    }
}