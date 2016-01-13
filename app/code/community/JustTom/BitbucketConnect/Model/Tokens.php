<?php
/**
 * Created by PhpStorm.
 * User: thomasburman
 * Date: 13/01/2016
 * Time: 06:34
 */ 
class JustTom_BitbucketConnect_Model_Tokens extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('justtom_bitbucketconnect/tokens');
    }

    protected function _getBitbucketProvider()
    {
        return Mage::helper('justtom_bitbucketconnect/provider')->getProvider();
    }

    public function getAccessToken($tokenCode)
    {
        $provider = $this->_getBitbucketProvider();
        $accessToken = $provider->getAccessToken(
            'authorization_code',
            array('code' => $tokenCode)
        );

        if($accessToken){
            $this->setAccessToken($accessToken->getToken());
            $this->setRefreshToken($accessToken->getRefreshToken());
            $this->setExpiresAt($accessToken->getExpires());
            $this->save();

            return true;
        }

        return false;
    }

}