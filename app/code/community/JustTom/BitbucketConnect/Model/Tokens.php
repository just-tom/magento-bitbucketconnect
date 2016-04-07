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
class JustTom_BitbucketConnect_Model_Tokens extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('justtom_bitbucketconnect/tokens');
    }

    /**
     * @param $customerId
     * @param $accessToken
     * @param $username
     *
     * @throws Exception
     */
    public function saveAccessToken($customerId, $accessToken, $username)
    {
        $entity = $this->load($customerId, 'customer_id');
        if ($entity->getEntityId()) {
            $entity->setAccessToken($accessToken->getToken());
            $entity->setRefreshToken($accessToken->getRefreshToken());
            $entity->setExpiresAt($accessToken->getExpires());
            $entity->save();
        } else {
            $this->setCustomerId($customerId);
            $this->setAccessToken($accessToken->getToken());
            $this->setRefreshToken($accessToken->getRefreshToken());
            $this->setExpiresAt($accessToken->getExpires());
            $this->setUsername($username);
            $this->save();
        }
    }

    protected function _getBitbucketProvider()
    {
        return Mage::helper('justtom_bitbucketconnect/provider')->getProvider();
    }

    protected function _getCurrentCustomerId()
    {
        return Mage::getModel('customer/session')->getCustomerId();
    }

    public function getAccessToken($tokenCode)
    {
        $customerId = $this->_getCurrentCustomerId();
        if ($customerId) {
            $provider = $this->_getBitbucketProvider();
            $accessToken = $provider->getAccessToken(
                'authorization_code',
                array('code' => $tokenCode)
            );

            if ($accessToken) {
                $resourceOwner = $provider->getResourceOwner($accessToken)
                    ->toArray();
                $this->saveAccessToken(
                    $customerId, $accessToken, $resourceOwner['username']
                );

                return true;
            }

            return false;
        }

        return false;
    }

    public function getAccessTokenWithRefresh($force = false, $customer = false)
    {
        $currentAccessDetails = $customer;
        if($currentAccessDetails){
            $customerId = $currentAccessDetails->getCustomerId();
        } else {
            $customerId = $customerId = $this->_getCurrentCustomerId();
            $currentAccessDetails = $this->load($customerId, 'customer_id');
        }

        if ($currentAccessDetails->hasExpired() || $force) {
            $provider = $this->_getBitbucketProvider();
            $newAccessToken = $provider->getAccessToken(
                'refresh_token', [
                    'refresh_token' => $currentAccessDetails->getRefreshToken(),
                ]
            );
            if ($newAccessToken) {
                $resourceOwner = $provider->getResourceOwner($newAccessToken)
                    ->toArray();
                $this->saveAccessToken(
                    $customerId, $newAccessToken, $resourceOwner['username']
                );

                return $newAccessToken;
            }
        }

        return $currentAccessDetails;
    }

    public function hasExpired()
    {
        $expires = $this->getExpiresAt();

        if (empty($expires)) {
            throw new RuntimeException('"expires" is not set on the token');
        }

        return $expires < time();
    }

    public function disconnectUser($id)
    {
        $entity = $this->load($id);
        $entity->delete();
    }

    public function testConnection()
    {
        $customerId = $customerId = $this->_getCurrentCustomerId();
        $currentAccessDetails = $this->load($customerId, 'customer_id');
        $provider = $this->_getBitbucketProvider();
        if(!$resourceOwner = $provider->getResourceOwner($currentAccessDetails->getData('access_token'))){
            throw new \Exception ('User Revoked');
        }
        return true;
    }
}