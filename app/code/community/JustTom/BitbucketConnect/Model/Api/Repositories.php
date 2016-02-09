<?php

class JustTom_BitbucketConnect_Model_Api_Repositories
    extends JustTom_BitbucketConnect_Model_Api_Abstract
{
    const BITBUCKET_VERSION = '2';
    const REPOSITORIES_URI = '/repositories/';

    public function getAllRepositories($username)
    {
        $request = $this->createRequest(
            'GET', self::BITBUCKET_VERSION, self::REPOSITORIES_URI.$username
        );

        if($response = $this->sendRequest($request)){
            if(array_key_exists('next', $response)){
                return $this->_iteratePages($response);
            }
            return $response;
        }
        return false;
    }
}