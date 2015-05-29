<?php

namespace CultuurNet\Auth\Guzzle;

use \CultuurNet\Auth\AuthorizeOptions;
use \CultuurNet\Auth\ServiceInterface;
use \CultuurNet\Auth\ConsumerCredentials;
use \CultuurNet\Auth\TokenCredentials;
use \CultuurNet\Auth\User;

use \Guzzle\Http\Client;
use \Guzzle\Http\Url;
use \Guzzle\Plugin\Oauth\OauthPlugin;


class Service extends OAuthProtectedService implements ServiceInterface
{
    /**
     * @param string $baseUrl
     * @param ConsumerCredentials $consumerCredentials
     */
    public function __construct($baseUrl, ConsumerCredentials $consumerCredentials)
    {
        parent::__construct($baseUrl, $consumerCredentials);
    }

    public function getRequestToken($callback = NULL) {
        $client = $this->getClient($callback);

        $request = $client->post('requestToken');

        $response = $request->send();

        // @todo Check for status 400 or 401 and throw an appropriate exception.
        // @todo Any other non-200 code is unexpected according to http://oauth.net/core/1.0a/ and should cause another kind of exception to be thrown.

        if ($response->getContentType() != 'application/x-www-form-urlencoded') {
            // @todo throw exception
        }

        parse_str($response->getBody(TRUE), $q);

        // @todo check if valid response
        $token = $q['oauth_token'];
        $secret = $q['oauth_token_secret'];

        if (!isset($q['oauth_callback_confirmed']) || $q['oauth_callback_confirmed'] !== 'true') {
            // @todo throw an exception
        }

        $tokenPair = new TokenCredentials($token, $secret);

        return $tokenPair;
    }

    /**
     * Get the URL of the authorization page to redirect the user to.
     *
     * @param TokenCredentials $temporaryCredentials
     *   Temporary credentials fetched with getRequestToken.
     * @param AuthorizeOptions $options
     *   Miscellaneous options accepted in the URL.
     * @return string
     *   The URL of the authorization page.
     */
    public function getAuthorizeUrl(TokenCredentials $temporaryCredentials, AuthorizeOptions $options = NULL) {
        // @todo check if token is not empty
        if ($options) {
            $query = AuthorizeOptionsQueryString::fromAuthorizeOptions($options);
        }
        else {
            $query = new AuthorizeOptionsQueryString();
        }

        $query->set('oauth_token', $temporaryCredentials->getToken());

        $url = $this->getUrlForPath('auth/authorize');
        $url->setQuery($query);

        return (string) $url;
    }

    /**
     * Fetches token credentials (access token and secret).
     *
     * @param TokenCredentials $temporaryCredentials
     *   The temporary token credentials (request token & secret).
     * @param string $oAuthVerifier
     *   The 'oauth_verifier' that was retrieved in the OAUth authorization step.
     * @return User
     */
    public function getAccessToken(TokenCredentials $temporaryCredentials, $oAuthVerifier) {
        $data = array(
            'oauth_verifier' => $oAuthVerifier,
        );

        $httpClientFactory = $this->getHttpClientFactory();
        $client = $httpClientFactory->createClient($this->baseUrl, $this->consumerCredentials, $temporaryCredentials);

        $response = $client->post('accessToken', NULL, $data)->send();
        if ($response->getContentType() != 'application/x-www-form-urlencoded') {
            // @todo throw exception
        }

        $body = $response->getBody(TRUE);

        parse_str($body, $q);
        // @todo check if valid response
        $token = $q['oauth_token'];
        $secret = $q['oauth_token_secret'];
        $userId = $q['userId'];

        $tokenCredentials = new TokenCredentials($token, $secret);

        $user = new User($userId, $tokenCredentials);

        return $user;
    }

    // @todo add method for registering a PSR-3 compliant logger
}
