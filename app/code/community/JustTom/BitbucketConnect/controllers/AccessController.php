<?php

/**
 * Cranbri Web Solutions (c) 2016 JustTom
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM
 * , OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
class JustTom_BitbucketConnect_AccessController
    extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

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
        $stateKey = Mage::getModel('core/session')->getData(
            'bitbucket_api_state'
        );
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

            $this->_redirect('customer/account');
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError(
                'Sorry something appears to have gone wrong'
            );
        }
    }
}