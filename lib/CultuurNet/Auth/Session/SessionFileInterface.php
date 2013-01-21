<?php

namespace CultuurNet\Auth\Session;

use \CultuurNet\Auth\Session;

interface SessionFileInterface
{
    /**
     * @param string $path
     * @return \CultuurNet\Auth\Session
     */
    public static function read($path);

    /**
     * @param \CultuurNet\Auth\Session $session
     * @param string $path
     * @return void
     */
    public static function write(Session $session, $path);
}
