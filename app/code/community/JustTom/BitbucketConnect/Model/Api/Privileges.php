<?php

class JustTom_BitbucketConnect_Model_Api_Privileges
    extends JustTom_BitbucketConnect_Model_Api_Abstract
{
    const BITBUCKET_VERSION = '1';
    const PRIVILEGE_URI = '/privileges/%s/%s/%s';

    public function setPermissionsOnRepository($owner, $slug, $customer)
    {
        $helper = Mage::helper('justtom_bitbucketconnect');
        $uri = $helper->__(self::PRIVILEGE_URI, $owner->getUsername(), $slug, $customer->getUsername());
        $request = $this->createRequest(
            'PUT', self::BITBUCKET_VERSION, $uri, $owner, array('body' => 'read')
        );

        if ($response = $this->sendRequest($request, $owner)) {
            return $response;
        }

        return false;
    }
}