<?php
/**
 * @file
 * Definition of \CultuurNet\Auth\Guzzle\JavaHttpRequestFactory.
 */

namespace CultuurNet\Auth\Guzzle;

use Guzzle\Http\Message\RequestFactory;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\QueryAggregator\DuplicateAggregator;

/**
 * HTTP request factory for CultuurNet's Java-based webservices.
 */
class JavaHttpRequestFactory extends RequestFactory
{
    /** @var string Class to instantiate for requests with a body */
    protected $entityEnclosingRequestClass = 'CultuurNet\\Auth\\Guzzle\\EntityEnclosingRequest';

    /**
     * {@inheritdoc}
     */
    public function create($method, $url, $headers = null, $body = null, array $options = array())
    {
        /** @var RequestInterface $request */
        $request = parent::create($method, $url, $headers, $body, $options);

        // Java web services do not expect [] behind multi-valued query string parameter names.
        // PHP: foo[]=1&foo[]=2
        // Java: foo=1&foo=2
        $request->getQuery()->setAggregator(new DuplicateAggregator());

        return $request;
    }
}
