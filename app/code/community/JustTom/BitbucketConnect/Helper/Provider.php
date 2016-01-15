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
class JustTom_BitbucketConnect_Helper_Provider
    extends Mage_Core_Helper_Abstract
{
    const XPATH_CONFIG_CLIENT_ID = 'justtom_bitbucketconnect/global/client_id';
    const XPATH_CONFIG_CLIENT_PASSWORD = 'justtom_bitbucketconnect/global/client_password';
    const XPATH_CONFIG_REDIRECT_URI = 'justtom_bitbucketconnect/global/redirect_uri';

    public function getProvider()
    {
        return new Stevenmaguire\OAuth2\Client\Provider\Bitbucket(
            array(
                'clientId'     => Mage::getStoreConfig(
                    self::XPATH_CONFIG_CLIENT_ID
                ),
                'clientSecret' => Mage::helper('core')->decrypt(
                    Mage::getStoreConfig(
                        self::XPATH_CONFIG_CLIENT_PASSWORD
                    )
                ),
                'redirectUri'  => Mage::getStoreConfig(
                    self::XPATH_CONFIG_REDIRECT_URI
                ),
            )
        );
    }
}