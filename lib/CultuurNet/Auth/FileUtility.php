<?php
/**
 * @file
 */

namespace CultuurNet\Auth;

class FileUtility
{
    protected $expand;

    /**
     * @var boolean
     */
    protected $expandTilde;

    /**
     * @var string
     */
    protected $userHomeDir;

    public function __construct()
    {
        if (function_exists('posix_getuid')) {
            $this->expandTilde = TRUE;

            $info = posix_getpwuid(posix_getuid());
            $this->userHomeDir = $info['dir'];
        }
    }

    public function expandPath($path)
    {
        if ($this->expandTilde && 0 === strpos($path, '~')) {
            $path = substr_replace($path, $this->userHomeDir, 0, 1);
        }

        return $path;
    }
}
