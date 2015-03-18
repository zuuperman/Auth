<?php

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\AuthorizeOptions;
use CultuurNet\Auth\ServiceInterface;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User;
use GuzzleHttp\Client;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Url;

class Service implements ServiceInterface
{
    use ContainsClient;

    /**
     * @var UserAuthenticatedClientFactory
     */
    private $clientFactory;

    /**
     * @param Client $client
     */
    public function __construct($client, UserAuthenticatedClientFactory $clientFactory)
    {
        $this->setClient($client);

        $this->clientFactory = $clientFactory;
    }

    /**
     * @inheritdoc
     */
    public function getRequestToken($callback = null)
    {
        $request = $this->client->createRequest(
            'POST',
            'requestToken'
        );

        /** @var PostBodyInterface $body */
        $body = $request->getBody();

        if ($callback) {
            $body->setField('oauth_callback', $callback);
        }

        $response = $this->client->send($request);

        // @todo Check for status 400 or 401 and throw an appropriate exception.
        // @todo Any other non-200 code is unexpected according to
        // http://oauth.net/core/1.0a/ and should cause another kind of
        // exception to be thrown.

        if ($response->getHeader('Content-Type') != 'application/x-www-form-urlencoded') {
            // @todo throw exception
        }

        $body = $response->getBody();

        parse_str((string) $body, $q);

        // @todo check if valid response
        $token = $q['oauth_token'];
        $secret = $q['oauth_token_secret'];

        if (!isset($q['oauth_callback_confirmed']) ||
            $q['oauth_callback_confirmed'] !== 'true') {
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
    public function getAuthorizeUrl(
        TokenCredentials $temporaryCredentials,
        AuthorizeOptions $options = null
    ) {
        $url = Url::fromString($this->client->getBaseUrl());

        if ($options) {
            $url->setQuery(
                AuthorizeOptionsQueryFactory::createQueryFromAuthorizeOptions(
                    $options
                )
            );
        }

        $url->getQuery()->set(
            'oauth_token',
            $temporaryCredentials->getToken()
        );

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
    public function getAccessToken(
        TokenCredentials $temporaryCredentials,
        $oAuthVerifier
    ) {
        $temporaryClient = $this->clientFactory->createClient($temporaryCredentials);

        $request = $temporaryClient->createRequest('POST', 'accessToken');

        /** @var PostBodyInterface $body */
        $body = $request->getBody();
        $body->setField('oauth_verifier', $oAuthVerifier);

        $response = $this->client->send($request);

        if ($response->getHeader('Content-Type') != 'application/x-www-form-urlencoded') {
            // @todo throw exception
        }

        $body = $response->getBody();

        parse_str((string) $body, $q);
        // @todo check if valid response
        $token = $q['oauth_token'];
        $secret = $q['oauth_token_secret'];
        $userId = $q['userId'];

        $tokenCredentials = new TokenCredentials($token, $secret);

        $user = new User($userId, $tokenCredentials);

        return $user;
    }
}
