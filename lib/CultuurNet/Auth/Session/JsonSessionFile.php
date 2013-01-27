<?php

namespace CultuurNet\Auth\Session;

use \CultuurNet\Auth\Json\JsonValidationException;
use \CultuurNet\Auth\ConsumerCredentials;
use \CultuurNet\Auth\TokenCredentials;
use \CultuurNet\Auth\User;
use \CultuurNet\Auth\Session;

use \JsonSchema\Validator;

class JsonSessionFile implements SessionFileInterface
{
    /**
     * @param string $path
     * @return \CultuurNet\Auth\Session
     */
    public static function read($path)
    {
        if (!file_exists($path)) {
            return new Session();
        }

        $json = file_get_contents($path);

        if (!$json) {
            return new Session();
        }

        $data = json_decode($json);

        // @todo Throw an exception if unable to parse.
        if (null === $data && JSON_ERROR_NONE !== json_last_error()) {

        }

        self::validateSchema($data, $path);

        $session = new Session();

        if (isset($data->baseUrls)) {
            foreach (get_object_vars($data->baseUrls) as $api => $baseUrl) {
                $session->setBaseUrl($api, $baseUrl);
            }
        }

        if (isset($data->consumer)) {
            $consumer = new ConsumerCredentials();
            $consumer->setKey($data->consumer->key);
            $consumer->setSecret($data->consumer->secret);
            $session->setConsumerCredentials($consumer);
        }

        if (isset($data->user)) {
            $tokenCredentials = new TokenCredentials($data->user->token, $data->user->secret);
            $user = new User($data->user->id, $tokenCredentials);
            $session->setUser($user);
        }

        return $session;
    }

    /**
     * @param \CultuurNet\Auth\Session $session
     * @param string $path
     */
    public static function write(Session $session, $path)
    {
        $hash = array();

        $consumerCredentials = $session->getConsumerCredentials();
        if (NULL != $consumerCredentials) {
            $hash['consumer'] = array(
                'key' => $consumerCredentials->getKey(),
                'secret' => $consumerCredentials->getSecret(),
            );
        }

        $user = $session->getUser();
        if (NULL !== $user) {
            $hash['user'] = array(
                'id' => $user->getId(),
                'token' => $user->getTokenCredentials()->getToken(),
                'secret' => $user->getTokenCredentials()->getSecret(),
            );
        }

        $baseUrls = $session->getBaseUrls();
        if (!empty($baseUrls)) {
            $hash['baseUrls'] = $baseUrls;
        }

        $json = json_encode($hash);

        // @todo Throw exception if unable to save.
        file_put_contents($path, $json);
    }

    protected static function validateSchema($data, $path)
    {
        $schemaFile = __DIR__ . '/../../../../res/session-schema.json';
        $schemaData = json_decode(file_get_contents($schemaFile));

        $validator = new Validator();
        $validator->check($data, $schemaData);

        if (!$validator->isValid()) {
            $errors = array();
            foreach ((array) $validator->getErrors() as $error) {
                $errors[] = ($error['property'] ? $error['property'] . ' : ' : '') . $error['message'];
            }
            throw new JsonValidationException('"'.$path.'" does not match the expected JSON schema', $errors);
        }

    }
}
