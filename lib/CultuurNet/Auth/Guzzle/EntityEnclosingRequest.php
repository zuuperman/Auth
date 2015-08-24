<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use Guzzle\Http\Message\EntityEnclosingRequest as OriginalEntityEnclosingRequest;

/**
 * Overriding EntityEnclosingRequestInterface::URL_ENCODED because
 * its charset property causes 401 Unauthorized responses on newer versions
 * of the UiTID backend. Bottom line conclusion: the Content-Type header is not
 * allowed to contain a charset property when its value equals
 * 'application/x-www-form-urlencoded'.
 */
class EntityEnclosingRequest extends OriginalEntityEnclosingRequest
{
    const URL_ENCODED = 'application/x-www-form-urlencoded';

    /**
     * @inheritdoc
     */
    protected function processPostFields()
    {
        if (!$this->postFiles) {
            $this->removeHeader('Expect')->setHeader('Content-Type', self::URL_ENCODED);
        } else {
            $this->setHeader('Content-Type', self::MULTIPART);
            if ($this->expectCutoff !== false) {
                $this->setHeader('Expect', '100-Continue');
            }
        }
    }
}
