<?php

class JustTom_BitbucketConnect_Model_Api_User
    extends JustTom_BitbucketConnect_Model_Api_Abstract
{
    const BITBUCKET_VERSION = '2';

    const USER_URI = '/users/';

    public function getUser($username)
    {
        $request = $this->createRequest(
            'GET', self::BITBUCKET_VERSION, self::USER_URI.$username
        );

        return $this->sendRequest($request);
    }
}