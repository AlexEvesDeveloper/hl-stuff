<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\FinancialRefereeStatus;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\FinancialRefereeType;
use Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FinancialRefereeStatusDesignationSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit'
        );
    }

    /**
     * SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        /** @var FinancialReferee[] $currentReferees */
        $currentReferees = array();

        /** @var FinancialReferee[] $futureReferees */
        $futureReferees = array();

        $form = $event->getForm();
        $now = new DateTime();

        // Iterate each financial referee in the collection and assign an appropriate financial referee status
        foreach ($form as $name => $value) {
            $financialRefereeForm = $form->get($name);
            $financialReferee = $financialRefereeForm->getData();

            if ($financialReferee instanceof FinancialReferee) {
                if ($financialRefereeForm->has('employmentStartDate')) {
                    if ($now < $financialReferee->getEmploymentStartDate()) {
                        // Future referee
                        $futureReferees[] = $financialReferee;
                    } elseif ($now >= $financialReferee->getEmploymentStartDate()) {
                        // Current referee
                        $currentReferees[] = $financialReferee;
                    }
                }
                else {
                    // Current referee
                    $currentReferees[] = $financialReferee;
                }
            }
            else {
                throw new UnexpectedTypeException($financialReferee, 'Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee');
            }
        }

        // Assign all the future referees
        foreach ($futureReferees as $futureReferee) {
            $futureReferee->setFinancialRefereeStatus(FinancialRefereeStatus::FUTURE_REFEREE);
        }

        // Choose a second referee, if more than one current referee exists
        $secondReferee = null;
        if (count($currentReferees) > 1) {
            foreach ($currentReferees as $currentReferee) {
                if (null === $secondReferee) {
                    $secondReferee = $currentReferee;
                }

                if (FinancialRefereeType::PENSION_STATEMENT == $currentReferee->getFinancialRefereeType() ||
                    FinancialRefereeType::PENSION_ADMINISTRATOR == $currentReferee->getFinancialRefereeType()) {
                    // Pension statements/administrators are favourable as second incomes
                    $secondReferee = $currentReferee;
                    break;
                }
            }
        }

        // Assign all the current and second referees
        foreach ($currentReferees as $currentReferee) {
            if ($currentReferee === $secondReferee) {
                $currentReferee->setFinancialRefereeStatus(FinancialRefereeStatus::SECOND_REFEREE);
            }
            else {
                $currentReferee->setFinancialRefereeStatus(FinancialRefereeStatus::CURRENT_REFEREE);
            }
        }
    }
}
