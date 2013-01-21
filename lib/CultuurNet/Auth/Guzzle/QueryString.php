<?php

namespace CultuurNet\Auth\Guzzle;

use \Guzzle\Http\QueryString as BaseQueryString;

class QueryString extends BaseQueryString
{
    public function __construct(array $data = null)
    {
        parent::__construct($data);
        $this->aggregator = array(__CLASS__, 'aggregateUsingDuplicates');
    }

    /**
     * @param callable|null $callback
     * @return \Guzzle\Http\QueryString|void
     * @throws \Exception
     */
    public function setAggregateFunction($callback) {
        throw new \Exception('Changing the aggregate function is not allowed');
    }
}
