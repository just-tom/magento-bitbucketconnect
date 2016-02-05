<?php

use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class JustTom_BitbucketConnect_Model_Api_Abstract
    extends Mage_Core_Model_Abstract
{
    const ERROR_UNAUTHORIZED = 'UNAUTHORIZED';

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
        try {
            return $this->_getBitbucketProvider()->getResponse($request);
        } catch (IdentityProviderException $e) {
            Mage::log($e->getMessage(), null, 'bitbucketconnect.log');
            $this->_handleException($e);
        }
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

    protected function _handleException($exception)
    {
        switch ($exception->getResponseBody()->getReasonPhrase()) {
            case self::ERROR_UNAUTHORIZED: {
                try{
                    Mage::getModel('justtom_bitbucketconnect/tokens')
                        ->getAccessTokenWithRefresh(true);
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'bitbucketconnect.log');
                    Mage::getModel('justtom_bitbucketconnect/tokens')
                        ->disconnectUser($this->_customer);
                    Mage::app()->getResponse()
                        ->setRedirect(Mage::getBaseUrl() . '/bitbucket/access/client');
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
        }
    }
}