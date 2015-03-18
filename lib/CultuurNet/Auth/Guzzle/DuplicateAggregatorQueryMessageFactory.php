<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use GuzzleHttp\Message\MessageFactory;

class DuplicateAggregatorQueryMessageFactory extends MessageFactory
{
    public function createRequest($method, $url, array $options = [])
    {
        $request = parent::createRequest($method, $url, $options);

        $query = $request->getQuery();

        $query->setAggregator($query::duplicateAggregator());

        return $request;
    }

}
