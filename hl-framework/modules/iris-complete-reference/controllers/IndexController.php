<?php

require_once __DIR__ . '/IrisCompleteReferenceAbstractController.php';

use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Iris\Referencing\Form\Type\RootStepType;
use Iris\Referencing\FormSet\Form\Type\SummaryType;
use Iris\Referencing\FormSet\Form\Type\TermsAndConditionsType;
use Iris\ProgressiveStore\Exception\PrototypeNotFoundException;
use Iris\Referencing\FormSet\Model\LinkRefHolder;
use Iris\Utility\DeclarationOwnership\DeclarationOwnership;

/**
 * Class IrisCompleteReference_IndexController
 *
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class IrisCompleteReference_IndexController extends IrisCompleteReferenceAbstractController
{
    /**
     * Case model class name
     */
    const MODEL_CASE_CLASS = 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase';

    /**
     * Application model class name
     */
    const MODEL_APPLICATION_CLASS = 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication';

    public function init()
    {
        parent::init();

        // If this is index, initialise everything before form flow is run
        if ('index' == $this->getRequest()->getActionName()) {

            if ($this->linkRef) {

                $application = $this
                    ->getIrisSystemContext()
                    ->getSystemApplicationClient()
                    ->getReferencingApplication(array(
                        'linkRef' => $this->linkRef,
                    ))
                ;

                $case = $this
                    ->getIrisSystemContext()
                    ->getSystemApplicationClient()
                    ->getReferencingCase(array(
                        'referencingCaseUuId' => $application->getReferencingCaseUuId()
                    ))
                ;

                // Convert datetime type
                $case
                    ->setTenancyStartDate(new \DateTime($case->getTenancyStartDate()))
                ;

                // Convert datetime type
                $application
                    ->setBirthDate(new \DateTime($application->getBirthDate()))
                    ->setCreatedAt(new \DateTime($application->getCreatedAt()))
                ;

                $canEmploymentChangeDuringTenancy = false;
                $hasMultipleJobOrPension = false;

                /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $financialReferee */
                foreach ($application->getFinancialReferees() as $financialReferee) {

                    $financialReferee->setEmploymentStartDate(new \DateTime($financialReferee->getEmploymentStartDate()));

                    if (2 == $financialReferee->getFinancialRefereeStatus()) {
                        $hasMultipleJobOrPension = true;
                    }

                    if (3 == $financialReferee->getFinancialRefereeStatus()) {
                        $canEmploymentChangeDuringTenancy = true;
                    }
                }

                /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $financialReferee */
                foreach ($application->getFinancialReferees() as $financialReferee) {

                    if (1 == $financialReferee->getFinancialRefereeStatus()) {

                        $financialReferee
                            ->setEmploymentChangeDuringTenancy($canEmploymentChangeDuringTenancy)
                            ->setMultipleJobOrPension($hasMultipleJobOrPension)
                        ;
                    }
                }

                $this
                    ->getSystemProgressiveStore()
                    ->clearPrototypes()
                    ->addPrototype($case)
                    ->addPrototype($application)
                    ->addPrototype(new LinkRefHolder($this->linkRef))
                    ->storePrototypes()
                ;
            }
        }

        $this->getFormFlow()->run();
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new SummaryType()),
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS),
            array(
                'removeBack' => true
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->isValid()) {
                // Details cannot be edited in summary by non-agents.
                // Therefore, we don't need to bother attempting to store.
                if ($form->get('next')->isClicked()) {
                    $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                }
            }
        }

        $case = $this->getSystemProgressiveStore()->getPrototypeByClass(self::MODEL_CASE_CLASS);
        $application = $this->getSystemProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

        $this->renderTwigView('/iris-complete-reference/summary-step.html.twig', array(
            'bodyTitle' => 'Summary',
            'form' => $form->createView(),
            'application' => $application,
            'case' => $case,
        ));
    }

    /**
     * Terms and conditions
     *
     * @return void
     */
    public function termsAndConditionsAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new TermsAndConditionsType()),
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {
                $paymentStatusResponse = null;

                $application = $this
                    ->getSystemProgressiveStore()
                    ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

                try {
                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\PaymentStatus $paymentStatusResponse */
                    $paymentStatusResponse = $this->getIrisSystemContext()->getSystemApplicationClient()->paymentStatus(array(
                        'applicationUuId' => $application->getReferencingApplicationUuId(),
                    ));
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                if ($paymentStatusResponse) {
                    if ($form->get('next')->isClicked()) {
                        $paymentStatus = $paymentStatusResponse->getPaymentStatus();

                        // note: Don't like the id numbers, should be strings
                        if (2 == $paymentStatus || 4 == $paymentStatus || 5 == $paymentStatus) {
                            // Payment complete, move straight to submission
                            $this
                                ->getIrisSystemContext()
                                ->getSystemApplicationClient()
                                ->submitApplication(array(
                                    'referencingApplicationUuId' => $application->getReferencingApplicationUuId(),
                                ));

                            $this->_helper->redirector->gotoUrlAndExit($this->params->homelet->domain . $this->getFormFlow()->getNextUrl());
                        }
                        else {
                            // Payment pending, create an order
                            $paymentOrderResponse = null;

                            try {
                                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\PaymentOrder $paymentOrderResponse */
                                $paymentOrderResponse = $this->getIrisSystemContext()->getSystemApplicationClient()->paymentOrder(array(
                                    'applicationUuId' => $application->getReferencingApplicationUuId(),
                                    'paymentTypes' => array(1),
                                    'redirectOnSuccessUrl' => $this->params->homelet->domain . $this->getFormFlow()->getNextUrl(),
                                ));
                            }
                            catch (ValidationException $e) {
                                $this->getFormValidationErrorBinder()->bind($form, $e);
                            }

                            if ($paymentOrderResponse) {
                                $this->_helper->redirector->gotoUrlAndExit($paymentOrderResponse->getPaymentPortalStartUrl());
                            }
                        }
                    }
                }
            }
        }

        $sysAppclientContext = $this->getIrisSystemContext()->getSystemApplicationClient();
        $declarationOwnership = new DeclarationOwnership();
        $agentSchemeNumber = $declarationOwnership->getAgentSchemeNumberByLinkRef($sysAppclientContext, $this->authSession->linkRef);
        $canDisplayDeclaration = $declarationOwnership->canDisplayDeclaration($agentSchemeNumber); 
        $this->renderTwigView('/iris-complete-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Terms and Conditions',
            'formTheme' => 'form/complete-terms-and-conditions-widgets.html.twig',
            'form' => $form->createView(),
            'canDisplayDeclaration' => $canDisplayDeclaration, 
        ));
    }

    public function submitAction()
    {
        $this->renderTwigView('/iris-complete-reference/submit.html.twig', array(
            'bodyTitle' => 'Reference Submitted',
            'application' => $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS),
        ));
    }

    /**
     * Get progressive store for agents
     *
     * @return \Iris\Referencing\FormSet\ProgressiveStore\SystemProgressiveStore
     */
    protected function getSystemProgressiveStore()
    {
        return $this->getContainer()->get('iris.referencing.form_set.progressive_store.system_progressive_store');
    }

    /**
     * Get form flow
     *
     * @return \Iris\FormFlow\AbstractFormFlow
     */
    protected function getFormFlow()
    {
        return $this->getContainer()->get('iris.referencing.form_flow.applicant_complete_reference_form_flow');
    }
}
