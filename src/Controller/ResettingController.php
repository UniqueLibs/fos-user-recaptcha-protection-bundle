<?php

namespace UniqueLibs\FOSUserRecaptchaProtectionBundle\Controller;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use UniqueLibs\FOSUserRecaptchaProtectionBundle\Services\ProtectionManager;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 * @author Marvin Rind        <kontakt@marvinrind.de>
 */
class ResettingController extends BaseResettingController
{
    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        /** @var ProtectionManager $protectionManager */
        $protectionManager = $this->container->get('uqe.fos_user_recaptcha_protection_bundle.protection_manager');

        $recaptchaSiteKey = null;
        if ($protectionManager->showRecaptchaOnResetPasswordPage($this->container->get('request'))) {
            $recaptchaSiteKey = $protectionManager->getConfigurationManager()->getRecaptchaSiteKey();
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.'.$this->getEngine(), array(
            'uniquelibs_recaptcha_site_key' => $recaptchaSiteKey,
        ));
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction()
    {
        /** @var ProtectionManager $protectionManager */
        $protectionManager = $this->container->get('uqe.fos_user_recaptcha_protection_bundle.protection_manager');

        $recaptchaSiteKey = null;
        if ($protectionManager->showRecaptchaOnResetPasswordPage($this->container->get('request'))) {
            $recaptchaSiteKey = $protectionManager->getConfigurationManager()->getRecaptchaSiteKey();

            $captcha = new ReCaptcha($protectionManager->getConfigurationManager()->getRecaptchaPrivateKey());

            $success = $captcha->verify($this->container->get('request')->get('g-recaptcha-response'), $this->container->get('request')->getClientIp())->isSuccess();

            if ($success !== true) {
                return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.'.$this->getEngine(), array(
                    'invalid_recaptcha' => true,
                    'uniquelibs_recaptcha_site_key' => $recaptchaSiteKey,
                ));
            }
        }

        $username = $this->container->get('request')->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.'.$this->getEngine(), array(
                'invalid_username' => $username,
                'uniquelibs_recaptcha_site_key' => $recaptchaSiteKey,
            ));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine());
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }
}
