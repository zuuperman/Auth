<?php
/**
 * @file
 * Definition of \CultuurNet\Auth\Guzzle\JavaHttpRequestFactory.
 */

namespace CultuurNet\Auth\Guzzle;

use Guzzle\Common\Collection;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\RequestFactory;

/**
 * HTTP request factory for CultuurNet's Java-based webservices.
 */
class JavaHttpRequestFactory extends RequestFactory
{
    /**
     * {@inheritdoc}
     */
    public function create($method, $url, $headers = null, $body = null)
    {
        $request = parent::create($method, $url, $headers, $body);

        // Java web services do not expect [] behind multi-valued query string parameter names.
        // PHP: foo[]=1&foo[]=2
        // Java: foo=1&foo=2
        $request->getQuery()->setAggregateFunction(array('\Guzzle\Http\QueryString', 'aggregateUsingDuplicates'));

        return $request;
    }
}
