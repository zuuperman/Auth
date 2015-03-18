<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use GuzzleHttp\Message\MessageFactory;

/**
 * Message factory for the Java based web services of CultuurNet.
 *
 * Java web services do not expect [] behind multi-valued query string parameter
 * names.
 * PHP: foo[]=1&foo[]=2
 * Java: foo=1&foo=2
 */
class DuplicateAggregatorQueryMessageFactory extends MessageFactory
{
    /**
     * @inheritdoc
     */
    public function createRequest($method, $url, array $options = [])
    {
        $request = parent::createRequest($method, $url, $options);

        $query = $request->getQuery();

        $query->setAggregator($query::duplicateAggregator());

        return $request;
    }
}
