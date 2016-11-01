<?php

namespace UniqueLibs\FOSUserRecaptchaProtectionBundle\Configuration;

/**
 * @author Marvin Rind <kontakt@marvinrind.de>
 */
class ConfigurationManager
{
    /**
     * @var string
     */
    protected $invalidLoginClass;

    /**
     * @var string
     */
    protected $loginAccessClass;

    /**
     * @var string
     */
    protected $recaptchaSiteKey;

    /**
     * @var string
     */
    protected $recaptchaPrivateKey;

    /**
     * @var int
     */
    protected $recaptchaNeededFails;

    /**
     * @var bool
     */
    protected $recaptchaOnPasswordReset;

    /**
     * @var bool
     */
    protected $recaptchaOnRegistration;

    /**
     * @var string
     */
    protected $lockTime;

    /**
     * @var int
     */
    protected $allowedRetries;

    /**
     * @var bool
     */
    protected $allowOnlyWhitelisted;

    /**
     * @param $invalidLoginClass
     * @param $loginAccessClass
     * @param $recaptchaSiteKey
     * @param $recaptchaPrivateKey
     * @param $recaptchaNeededFails
     * @param $recaptchaOnPasswordReset
     * @param $lockTime
     * @param $allowedRetries
     * @param $allowOnlyWhitelisted
     */
    public function __construct(
        $invalidLoginClass,
        $loginAccessClass,
        $recaptchaSiteKey,
        $recaptchaPrivateKey,
        $recaptchaNeededFails,
        $recaptchaOnPasswordReset,
        $recaptchaOnRegistration,
        $lockTime,
        $allowedRetries,
        $allowOnlyWhitelisted
    )
    {
        $this->invalidLoginClass = $invalidLoginClass;
        $this->loginAccessClass = $loginAccessClass;
        $this->recaptchaSiteKey = $recaptchaSiteKey;
        $this->recaptchaPrivateKey = $recaptchaPrivateKey;
        $this->recaptchaNeededFails = $recaptchaNeededFails;
        $this->recaptchaOnPasswordReset = $recaptchaOnPasswordReset;
        $this->recaptchaOnRegistration = $recaptchaOnRegistration;
        $this->lockTime = $lockTime;
        $this->allowedRetries = $allowedRetries;
        $this->allowOnlyWhitelisted = $allowOnlyWhitelisted;
    }

    /**
     * @return string
     */
    public function getInvalidLoginClass()
    {
        return $this->invalidLoginClass;
    }

    /**
     * @return string
     */
    public function getLoginAccessClass()
    {
        return $this->loginAccessClass;
    }

    /**
     * @return string
     */
    public function getRecaptchaSiteKey()
    {
        return $this->recaptchaSiteKey;
    }

    /**
     * @return string
     */
    public function getRecaptchaPrivateKey()
    {
        return $this->recaptchaPrivateKey;
    }

    /**
     * @return int
     */
    public function getRecaptchaNeededFails()
    {
        return $this->recaptchaNeededFails;
    }

    /**
     * @return boolean
     */
    public function isRecaptchaOnPasswordReset()
    {
        return $this->recaptchaOnPasswordReset;
    }

    /**
     * @return boolean
     */
    public function isRecaptchaOnRegistration()
    {
        return $this->recaptchaOnRegistration;
    }

    /**
     * @return string
     */
    public function getLockTime()
    {
        return $this->lockTime;
    }

    /**
     * @return int
     */
    public function getAllowedRetries()
    {
        return $this->allowedRetries;
    }

    /**
     * @return boolean
     */
    public function isAllowOnlyWhitelisted()
    {
        return $this->allowOnlyWhitelisted;
    }
}