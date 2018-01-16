<?php

namespace OWolf\Laravel;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;

class OAuthCache
{
    /**
     * @var array
     */
    protected $resourceDetails = [];

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken                $token
     * @param  \League\OAuth2\Client\Provider\ResourceOwnerInterface  $resourceDetails
     * @return $this
     */
    public function setResourceDetail(AccessToken $token, ResourceOwnerInterface $resourceDetails)
    {
        $hash = $this->hash($token);
        $this->resourceDetails[$hash] = $resourceDetails;
        return $this;
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface|null
     */
    public function getResourceDetailCache(AccessToken $token)
    {
        $hash = $this->hash($token);
        return array_get($this->resourceDetails, $hash);
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return string
     */
    protected function hash(AccessToken $token)
    {
        return spl_object_hash($token) . $token->getToken();
    }

    /**
     * @return $this
     */
    public function clearResourceDetailCache()
    {
        $this->resourceDetails = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function clearAllCache()
    {
        $this->clearResourceDetailCache();
        return $this;
    }
}