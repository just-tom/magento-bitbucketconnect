<?php

class JustTom_BitbucketConnect_Model_Api_Abstract
    extends Mage_Core_Model_Abstract
{
    public function createRequest($method, $api, $uri)
    {
        return $this->_getBitbucketProvider()->getAuthenticatedRequest(
            $method,
            $this->_buildUri($api, $uri),
            $this->_getCustomerAccessToken()
        );
    }

    public function sendRequest($request)
    {
        return $this->_getBitbucketProvider()->getResponse($request);
    }

    protected function _getBitbucketProvider()
    {
        return Mage::helper('justtom_bitbucketconnect/provider')->getProvider();
    }

    protected function _getCustomerAccessToken()
    {
        $id = Mage::getModel('customer/session')->getCustomerId();
        $accessToken = Mage::getModel('justtom_bitbucketconnect/tokens')->load(
            $id, 'customer_id'
        );
        if (!$accessToken->getEntityId()) {
            Mage::getModel('customer/session')->addError(
                'could not find valid token for user'
            );
            Mage::throwException('could not find valid token for user'.$id);
        }

        return $accessToken->getData('access_token');
    }

    protected function _buildUri($apiType, $uri)
    {
        $baseUrl = Mage::helper('justtom_bitbucketconnect/provider')
            ->getApiType($apiType);

        return $baseUrl.$uri;
    }
}