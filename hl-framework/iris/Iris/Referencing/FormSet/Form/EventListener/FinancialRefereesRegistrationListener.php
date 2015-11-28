<?php

namespace Iris\Referencing\FormSet\Form\EventListener;

use Barbondev\IRISSDK\Common\Model\FinancialReferee;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Iris\Referencing\Form\Type\FinancialRefereeType;
use Symfony\Component\Form\FormInterface;

/**
 * Class FinancialRefereesRegistrationListener
 *
 * @package Iris\Referencing\FormSet\Form\EventListener
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class FinancialRefereesRegistrationListener implements EventSubscriberInterface
{
    /**
     * Referee constants, as used by IRIS
     */
    const CURRENT_REFEREE = 1;
    const SECOND_REFEREE = 2;
    const FUTURE_REFEREE = 3;

    /**
     * Order of appearance of referee sub forms
     *
     * @var array
     */
    private static $refereeOrder = array(
        self::CURRENT_REFEREE,
        self::SECOND_REFEREE,
        self::FUTURE_REFEREE,
    );

    /**
     * Copy of the forms submitted data for child form resubmission
     *
     * @var array
     */
    private $submittedData = array();

    /**
     * Find the index of an element in a list of form elements with the $statusId status
     * enumeration.
     *
     * @param FormInterface $elements
     * @param int $statusId
     * @return int|null
     */
    private function findFormElementIndexByStatusId(FormInterface $elements, $statusId)
    {
        /** @var FormInterface $element */
        foreach ($elements as $key => $element) {
            if ($element->get('financialRefereeStatus')->getData() == $statusId) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Find an element in a list of model elements with the $statusId status
     * enumeration.
     *
     * @param array $elements
     * @param int $statusId
     * @return FinancialReferee|null
     */
    private function findModelElementByStatusId(array $elements, $statusId)
    {
        /** @var FinancialReferee $element */
        foreach ($elements as $element) {
            if ($element->getFinancialRefereeStatus() == $statusId) {
                return $element;
            }
        }

        return null;
    }

    /**
     * POST_SET_DATA event handler
     *
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();

        // Current financial referee is always displayed
        if (null === $this->findFormElementIndexByStatusId($form, self::CURRENT_REFEREE)) {
            $form->add(count($form), new FinancialRefereeType(), array(
                'refereeStatus' => self::CURRENT_REFEREE,
            ));
        }
    }

    /**
     * PRE_SUBMIT event handler
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Add extra fields from input
        // Copied from the ResizeFormListener Symfony class
        // This can cause duplicate referee forms to be added if the form is being POSTed
        // from the client with second and future referees, however form order has forms
        // at the top of the collection as higher priority and all sub forms are removed
        // when the form is cleaned as a result of the submit event handler.
        foreach ($data as $name => $value) {
            if (!$form->has($name)) {
                $form->add($name, new FinancialRefereeType());
            }
        }

        if (null === $this->findFormElementIndexByStatusId($form, self::SECOND_REFEREE)) {
            $form->add(count($form), new FinancialRefereeType(), array(
                'refereeStatus' => self::SECOND_REFEREE,
            ));
        }

        if (null === $this->findFormElementIndexByStatusId($form, self::FUTURE_REFEREE)) {
            $form->add(count($form), new FinancialRefereeType(), array(
                'refereeStatus' => self::FUTURE_REFEREE,
            ));
        }

        // Capture the submitted data for when the form is rebuild in the next event
        $this->submittedData = $data;
    }

    /**
     * SUBMIT event handler
     *
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $currentRefereeModel = $this->findModelElementByStatusId($data, self::CURRENT_REFEREE);

        // Remove second referee if not required
        if (!$currentRefereeModel->getMultipleJobOrPension()) {
            while (($secondRefereeIndex = $this->findFormElementIndexByStatusId($form, self::SECOND_REFEREE)) !== null) {
                $form->remove($secondRefereeIndex);
            }
        }

        // Remove future referee if not required
        if (!$currentRefereeModel->getEmploymentChangeDuringTenancy()) {
            while (($futureRefereeIndex = $this->findFormElementIndexByStatusId($form, self::FUTURE_REFEREE)) !== null) {
                $form->remove($futureRefereeIndex);
            }
        }

        // Reorder form elements by removing form elements and re-adding, copying the refereeStatus through
        // and applying reorder to the model data
        // Step 1. Place a copy of the model in to a new reordered array for each referee that is required
        $reorderedLayout = array();
        $reorderedData = array();
        foreach (self::$refereeOrder as $orderedRefereeStatusId) {
            // Check if form element exists in form
            // If the element is not required, checks above will have removed it.
            $formElementIndex = $this->findFormElementIndexByStatusId($form, $orderedRefereeStatusId);

            // Find model
            $refereeModel = $this->findModelElementByStatusId($data, $orderedRefereeStatusId);

            if (null === $refereeModel && null !== $formElementIndex) {
                // Form exists but model does not, create a new model to match the required form
                $refereeModel = new FinancialReferee();
                $refereeModel->setFinancialRefereeStatus($orderedRefereeStatusId);
            }

            if (null !== $refereeModel && null !== $formElementIndex) {
                // Model and form exist, add the model to the reordered models
                $reorderedLayout[] = array('model' => $refereeModel, 'index' => $formElementIndex);
                $reorderedData[] = $refereeModel;
            }
        }

        // Step 2. Strip all form elements
        foreach ($form as $name => $element) {
            $form->remove($name);
        }

        // Step 3. Rebuild the form in the correct order using the new model data array
        // and the original submission data
        foreach ($reorderedLayout as $refereeIndex => $refereeModel) {
            /*
             * Re-add form and force submission
             *
             * Forms must be flagged as 'submitted' AND synchronised for the
             * validation mapper to bind validation violation errors to the form.
             *
             * If the form is removed and re-added without submission, the form does
             * not have this flag set to true. Therefore, to get around this,
             * we manually force the submission of the form here.
             *
             * Note that the form must also submit with the raw data provided by the
             * client. The submit method does not accept model data and results in
             * the synchronised flag not getting set which is also required, so we
             * must keep a copy of the submission data in the pre-submit event
             * handler above.
             *
             * See https://barbondev.atlassian.net/browse/HII-187 for more details
             */
            $form
                ->add($refereeIndex, new FinancialRefereeType(),
                    array(
                        'refereeStatus' => $refereeModel['model']->getFinancialRefereeStatus(),
                    ))
                ->get($refereeIndex)
                ->setData(isset($refereeModel['model']) ? $refereeModel['model'] : null)
                ->submit((array_key_exists($refereeModel['index'], $this->submittedData) ?
                    $this->submittedData[$refereeModel['index']] : array()))
            ;
        }

        $event->setData($reorderedData);
    }

    /**
     * postSubmit form event listener
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        // If pension statement or self assessment address must be null
        $financialRefereesToModify = $this->getFinancialRefereesByType($event->getData(), array(
            FinancialRefereeType::PENSION_STATEMENT,
            FinancialRefereeType::SELF_ASSESSMENT,
        ));

        /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $financialRefereeToModify  */
        foreach ($financialRefereesToModify as $financialRefereeToModify) {
            $financialRefereeToModify->setAddress(null);
        }
    }

    /**
     * Get financial referees that match type ids
     *
     * @param array<\Barbondev\IRISSDK\Common\Model\FinancialReferee> $inputFinancialReferees
     * @param array<integer> $typeIds
     * @return array<\Barbondev\IRISSDK\Common\Model\FinancialReferee>
     */
    private function getFinancialRefereesByType(array $inputFinancialReferees, array $typeIds)
    {
        $financialReferees = array();

        /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $financialReferee  */
        foreach ($inputFinancialReferees as $financialReferee) {
            if (in_array($financialReferee->getFinancialRefereeType(), $typeIds)) {
                $financialReferees[] = $financialReferee;
            }
        }

        return $financialReferees;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => array('postSetData', -1),
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }
}
