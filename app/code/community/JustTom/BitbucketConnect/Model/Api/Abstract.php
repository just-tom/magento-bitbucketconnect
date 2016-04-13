<?php

use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class JustTom_BitbucketConnect_Model_Api_Abstract
    extends Mage_Core_Model_Abstract
{
    const ERROR_UNAUTHORIZED = 'UNAUTHORIZED';

    protected $_pagedResults = array();
    protected $_request;
    protected $_method;
    protected $_url;
    protected $_body;

    public function createRequest($method, $api, $uri, $token = false, $body = array())
    {
        $this->_method = $method;
        $this->_url = $this->_buildUri($api, $uri);
        $this->_token = ($token != false) ? $token->getData('access_token') : $this->_getCustomerAccessToken();
        $this->_body = $body;
        return $this->_getBitbucketProvider()->getAuthenticatedRequest(
            $this->_method,
            $this->_url,
            $this->_token,
            $this->_body
        );
    }

    public function createRequestWithoutBuild($method, $uri, $token = false, $body = array())
    {
        $this->_method = $method;
        $this->_url = $uri;
        $this->_token = ($token != false) ? $token->getData('access_token') : $this->_getCustomerAccessToken();
        $this->_body = $body;
        return $this->_getBitbucketProvider()->getAuthenticatedRequest(
            $this->_method,
            $this->_url,
            $this->_token,
            $this->_body
        );
    }

    public function sendRequest($request, $currentToken = false)
    {
        try {
            $this->_request = $request;
            return $this->_getBitbucketProvider()->getResponse($request);
        } catch (IdentityProviderException $e) {
            Mage::log($e->getMessage(), null, 'bitbucketconnect.log');
            return $this->_handleException($e, $currentToken);
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

    protected function _handleException($exception, $currentToken)
    {
        switch ($exception->getResponseBody()->getReasonPhrase()) {
            case self::ERROR_UNAUTHORIZED: {
                try{
                    $token = Mage::getModel('justtom_bitbucketconnect/tokens')
                        ->getAccessTokenWithRefresh(true, $currentToken);
                    $request = $this->_getBitbucketProvider()->getAuthenticatedRequest(
                        $this->_method,
                        $this->_url,
                        $token->getData('access_token'),
                        $this->_body
                    );
                    return $this->sendRequest($request);
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

    protected function _iteratePages($response)
    {
        $this->_pagedResults = array_merge($this->_pagedResults, $response['values']);
        if(array_key_exists('next', $response) && $response['next'] != null){
            $nextRequest = $this->createRequestWithoutBuild('GET',$response['next']);
            $response = $this->sendRequest($nextRequest);
            $this->_iteratePages($response);
        }
        return $this->_setCollectionItems($this->_pagedResults);
    }

    protected function _setCollectionItems($results)
    {
        $collection = new Varien_Data_Collection();
        foreach($results as $key => $value){
            $object = new Varien_Object();
            $object->setData($value);
            $collection->addItem($object);
        }

        return $collection;
    }
}