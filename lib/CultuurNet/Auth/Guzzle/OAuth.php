<?php

namespace CultuurNet\Auth\Guzzle;

use Guzzle\Plugin\Oauth\OauthPlugin;

class OAuth extends OauthPlugin
{
    /**
     * Convert booleans to strings, removed unset parameters, and sorts arrays
     *
     * @param array $data Data array
     * @return array
     */
    protected function prepareParameters($data)
    {
        foreach ($data as $key => &$value) {
            switch (gettype($value)) {
                case 'NULL':
                    unset($data[$key]);
                    break;
                case 'array':
                    usort($value, 'strcmp');
                    $data[$key] = self::prepareParameters($value);
                    break;
                case 'boolean':
                    $data[$key] = $value ? 'true' : 'false';
                    break;
            }
        }

        return $data;
    }
}
