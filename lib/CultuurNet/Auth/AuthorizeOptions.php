<?php

namespace CultuurNet\Auth;

class AuthorizeOptions
{
    const TYPE_REGULAR = 'regular';

    const TYPE_FORCE_LOGIN = 'forcelogin';

    const TYPE_REGISTER = 'register';

    const VIA_GOOGLE = 'google';

    const VIA_TWITTER = 'twitter';

    const VIA_FACEBOOK = 'facebook';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $skipAuthorization;

    /**
     * @var bool
     */
    protected $skipConfirmation;

    /**
     * @var string
     */
    protected $via;

    /**
     * @var string
     *   2-letter ISO code of the language
     */
    protected $lang;

    /**
     *
     */
    public function __construct()
    {
        $this->type = self::TYPE_REGULAR;

        $this->skipConfirmation = FALSE;
        $this->skipAuthorization = FALSE;
    }

    /**
     * @return AuthorizeOptions
     */
    public function setTypeRegular()
    {
        return $this->setType(self::TYPE_REGULAR);
    }

    /**
     * @return AuthorizeOptions
     */
    public function setTypeRegister()
    {
        return $this->setType(self::TYPE_REGISTER);
    }

    /**
     * @return AuthorizeOptions
     */
    public function setTypeForceLogin()
    {
        return $this->setType(self::TYPE_FORCE_LOGIN);
    }

    /**
     * @param string $type
     * @return AuthorizeOptions
     */
    public function setType($type) {
        // @todo Throw exception if invalid type.

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param bool $toggle;
     * @return AuthorizeOptions
     */
    public function setSkipConfirmation($toggle = TRUE)
    {
        $this->skipConfirmation = $toggle;
        return $this;
    }

    public function getSkipConfirmation()
    {
        return $this->skipConfirmation;
    }

    /**
     * @param bool $toggle
     * @return AuthorizeOptions
     */
    public function setSkipAuthorization($toggle = TRUE)
    {
        $this->skipAuthorization = $toggle;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSkipAuthorization()
    {
        return $this->skipAuthorization;
    }

    /**
     * @param string $via
     * @return AuthorizeOptions
     */
    public function setVia($via)
    {
        // @todo Throw exception if invalid type.

        $this->via = $via;
        return $this;
    }

    /**
     * @return AuthorizeOptions
     */
    public function setViaFacebook()
    {
        return $this->setVia(self::VIA_FACEBOOK);
    }

    /**
     * @return AuthorizeOptions
     */
    public function setViaTwitter()
    {
        return $this->setVia(self::VIA_TWITTER);
    }

    /**
     * @return AuthorizeOptions
     */
    public function setViaGoogle()
    {
        return $this->setVia(self::VIA_GOOGLE);
    }

    /**
     * @return string
     */
    public function getVia()
    {
        return $this->via;
    }

    /**
     * @param string $language 2-letter ISO code of the language
     * @return AuthorizeOptions
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }
}
