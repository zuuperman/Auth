<?php

namespace CultuurNet\Auth\Guzzle;

use \CultuurNet\Auth\AuthorizeOptions;
use GuzzleHttp\Query;

class AuthorizeOptionsQueryFactory
{
    /**
     * @param AuthorizeOptions $options
     *
     * @return Query
     */
    public static function createQueryFromAuthorizeOptions(AuthorizeOptions $options)
    {
        $q = new Query();
        $q->setAggregator($q::duplicateAggregator());

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
