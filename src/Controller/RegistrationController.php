<?php

namespace UniqueLibs\FOSUserRecaptchaProtectionBundle\Controller;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseRegistrationController;
use Symfony\Component\Translation\TranslatorInterface;
use UniqueLibs\FOSUserRecaptchaProtectionBundle\Services\ProtectionManager;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 * @author Marvin Rind        <kontakt@marvinrind.de>
 */
class RegistrationController extends BaseRegistrationController
{
    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        /** @var ProtectionManager $protectionManager */
        $protectionManager = $this->container->get('uqe.fos_user_recaptcha_protection_bundle.protection_manager');

        $recaptchaSiteKey = null;
        if ($protectionManager->showRecaptchaOnRegistration($this->container->get('request'))) {
            $recaptchaSiteKey = $protectionManager->getConfigurationManager()->getRecaptchaSiteKey();

            if ('POST' === $this->container->get('request')->getMethod()) {
                $captcha = new ReCaptcha($protectionManager->getConfigurationManager()->getRecaptchaPrivateKey());

                $success = $captcha->verify($this->container->get('request')->get('g-recaptcha-response'), $this->container->get('request')->getClientIp())->isSuccess();

                if ($success !== true) {

                    /** @var TranslatorInterface $translator */
                    $translator = $this->container->get('translator');

                    $this->setFlash('error', $translator->trans('recaptcha.invalid', array(), 'UniqueLibsFOSUserRecaptchaProtectionBundle'));

                    return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
                        'form' => $form->createView(),
                        'uniquelibs_recaptcha_site_key' => $recaptchaSiteKey,
                    ));
                }
            }
        }

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            if ($authUser) {
                $this->authenticateUser($user, $response);
            }

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
            'uniquelibs_recaptcha_site_key' => $recaptchaSiteKey,
        ));
    }
}
