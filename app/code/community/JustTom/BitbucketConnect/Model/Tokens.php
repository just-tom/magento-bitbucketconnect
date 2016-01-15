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
     *
     * @throws Exception
     */
    public function saveAccessToken($customerId, $accessToken)
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
                $this->saveAccessToken($customerId, $accessToken);

                return true;
            }

            return false;
        }

        return false;
    }

    public function getAccessTokenWithRefresh()
    {
        $currentAccessDetails = Mage::getModel(
            'justtom_bitbucketconnect/tokens'
        )
            ->load($this->_getCurrentCustomerId(), 'customer_id');

        if ($currentAccessDetails->hasExpired()) {
            $newAccessToken = $this->_getBitbucketProvider()->getAccessToken(
                'refresh_token', [
                    'refresh_token' => $currentAccessDetails->getRefreshToken(),
                ]
            );
            if ($newAccessToken) {
                $this->saveAccessToken(
                    $this->_getCurrentCustomerId(), $newAccessToken
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
}