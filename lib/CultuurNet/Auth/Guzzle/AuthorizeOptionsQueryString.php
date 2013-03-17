<?php

namespace CultuurNet\Auth\Guzzle;

use \CultuurNet\Auth\AuthorizeOptions;
use \Guzzle\Http\QueryString;

class AuthorizeOptionsQueryString extends QueryString
{
    /**
     * return AuthorizeOptionsQueryString
     */
    public static function fromAuthorizeOptions(AuthorizeOptions $options)
    {
        $q = new static();

        $q->set('type', $options->getType());

        $via = $options->getVia();
        if ($via) {
            $q->set('via', $via);
        }

        if ($options->getSkipConfirmation()) {
            $q->set('skipConfirmation', 'true');
        }

        if ($options->getSkipAuthorization()) {
            $q->set('skipAuthorization', 'true');
        }

        $lang = $options->getLang();
        if ($lang) {
            $q->set('lang', $lang);
        }

        return $q;
    }
}
