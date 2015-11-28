<?php

namespace RRP\EventListener;

use RRP\Event\ReferredEvent;
use RRP\Referral\RentRecoveryPlusReferral;

/**
 * Class DispatchReferralEmailListener
 *
 * @package RRP\EventListener
 * @author Alex Eves <alex.eves@barbon.com>
 */
class DispatchReferralEmailListener
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Zend_Config
     */
    protected $configHandler;

    /**
     * @var Application_Core_Mail
     */
    protected $mailManager;

    /**
     * DispatchReferralEmailListener Constructor.
     *
     * @param \Twig_Environment $twig
     * @param $configHandler
     * @param $mailManager
     */
    public function __construct(\Twig_Environment $twig, $configHandler, $mailManager)
    {
        $this->twig = $twig;
        $this->configHandler = $configHandler;
        $this->mailManager = $mailManager;
    }

    /**
     * Dispatched each time the listener is triggered.
     *
     * @param ReferredEvent $event
     */
    public function onReferral(ReferredEvent $event)
    {
        $referral = $event->getReferral();
        $this->buildAndSendEmail($referral);
    }

    /**
     * Build and send e-mail.
     *
     * @param RentRecoveryPlusReferral $referral
     */
    private function buildAndSendEmail(RentRecoveryPlusReferral $referral)
    {
        $toAddress = $this->configHandler->connect->settings->rentRecoveryPlus->referral->emailToAddress;
        $fromAddress = $this->configHandler->connect->settings->rentRecoveryPlus->referral->emailFromAddress;
        $toName = $this->configHandler->connect->settings->rentRecoveryPlus->referral->emailToName;
        $fromName = $this->configHandler->connect->settings->rentRecoveryPlus->referral->emailFromName;

        $this->mailManager
            ->setTo($toAddress, $toName)
            ->setFrom($fromAddress, $fromName)
            ->setSubject($this->getEmailSubject($referral))
            ->setBodyText($this->getEmailBodyText($referral))
            ->send();
    }

    /**
     * Get e-mail subject.
     *
     * @param $referral
     * @return string
     */
    private function getEmailSubject(RentRecoveryPlusReferral $referral)
    {
        return str_replace(
            '{$policyNumber}',
            $referral->getPolicyNumber(),
            $this->configHandler->connect->settings->rentRecoveryPlus->referral->emailSubject
        );
    }

    /**
     * Get e-mail body text.
     *
     * @param RentRecoveryPlusReferral $referral
     * @return string
     */
    private function getEmailBodyText(RentRecoveryPlusReferral $referral)
    {
        return $this->twig->render(
            'rent-recovery-plus-referral-mail.plain.twig',
            array(
                'agentName' => $referral->getAgent()->name,
                'agentSchemeNumber' => $referral->getAgent()->agentSchemeNumber,
                'referralReasons' => $referral->getAllReferralReasons()
            )
        );
    }
}