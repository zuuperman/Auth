<?php

namespace CultuurNet\Auth;

/**
 * @see http://oauth.net/core/1.0a/
 */
interface ServiceInterface
{
    /**
     * @return TokenCredentials
     */
    public function getRequestToken();

    /**
     * Get the URL of the authorization page to redirect the user to.
     *
     * @param string $token
     *   A request token fetched with getRequestToken().
     * @param AuthorizeOptions $options
     *   Options specific to authorization.
     * @return string
     *   The URL of the authorization page.
     */
    public function getAuthorizeUrl(TokenCredentials $requestTokenPair, AuthorizeOptions $options = NULL);

    /**
     * @param string $oAuthToken
     * @param string $oAuthVerifier
     * @return User
     */
    public function getAccessToken(TokenCredentials $requestTokenPair, $oAuthVerifier);
}
