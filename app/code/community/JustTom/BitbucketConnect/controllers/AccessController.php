<?php

class JustTom_BitbucketConnect_AccessController
    extends Mage_Core_Controller_Front_Action
{
    public function clientAction()
    {
        $bitbucketProvider = Mage::helper('justtom_bitbucketconnect/provider')
            ->getProvider();
        $authUrl = $bitbucketProvider->getAuthorizationUrl();
        try {
            Mage::getModel('core/session')->setData(
                'bitbucket_api_state', $bitbucketProvider->getState()
            );
            header('Location: '.$authUrl);
            exit;
        } catch (\Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    public function callbackAction()
    {
        $stateKey = Mage::getModel('core/session')->getData('bitbucket_api_state');
        $parameters = $this->getRequest()->getParams();
        if (!isset($parameters['state'])
            && $parameters['state'] != $stateKey
        ) {
            Mage::throwException('States do not match');
        }
        try {
            $model = Mage::getModel('justtom_bitbucketconnect/tokens');
            $result = $model->getAccessToken($parameters['code']);
            if (!$result) {
                Mage::throwException('Could not get access token');
            }

            return $this;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError(
                'Sorry something appears to have gone wrong'
            );
        }
    }
}