<?php

require_once __DIR__ . '/IrisRefereeAbstractController.php';

use Iris\Referencing\Form\Type\RootStepType;
use Iris\Referencing\Form\Type\ContractEmployerRefereeResponseType;
use Iris\Referencing\Form\Type\EmployerRefereeResponseType;
use Iris\Referencing\Form\Type\LettingRefereeResponseType;
use Iris\Referencing\Form\Type\PensionProviderRefereeResponseType;
use Iris\Referencing\Form\Type\SelfEmployedRefereeResponseType;

/**
 * Class Referee_IndexController
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
class Referee_IndexController extends IrisRefereeAbstractController
{
    /**
     * Letting referee action
     *
     * @return void
     */
    public function lettingRefereeAction()
    {
        // Get application details such as applicant name and application reference number
        $application = $this->getReferencingApplication($this->linkRef);
        $applicantName = $application->getFullName();
        $applicationReferenceNumber = $application->getReferenceNumber();

        // Get details about letting referee
        $lettingReferee = $application->getLettingReferee();
        $refereeType = $lettingReferee->getType();
        $refereeName = $lettingReferee->getName();

        $form = $this->getFormFactory()->create(
            new RootStepType(new LettingRefereeResponseType()),
            null,
            array(
                'removeBack' => true,
                'removeNext' => true,
                'removeSubmit' => false,
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {
            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                // Get form data and add link reference into the array
                $formData = array_merge(
                    $form->get('step')->getData(),
                    array('linkRef' => $this->linkRef)
                );

                // Add IP address to form data
                $formData['refereeIpAddress'] = $this->getSymfonyRequest()->getClientIp();

                // Store form content
                $this->getIrisSystemContext()->getSystemApplicationClient()->submitLetting(
                    $formData
                );

                // Show generic thank you message
                $this->_helper->redirector->gotoUrl('/referee/completed');

            }
        }

        $this->renderTwigView('letting-referee.html.twig', array(
            'bodyTitle' => 'Letting Referee',
            'applicantName' => $applicantName,
            'applicationReferenceNumber' => $applicationReferenceNumber,
            'refereeType' => $refereeType,
            'refereeName' => $refereeName,
            'formTheme' => 'referee-widgets.html.twig',
            'form' => $form->createView(),
        ));
    }

    /**
     * Contract employer referee action
     *
     * @return void
     */
    public function contractEmployerRefereeAction()
    {
        // Get length of tenancy term from the case to show in form question
        $case = $this->getReferencingCase($this->linkRef);
        $tenancyTerm = $case->getTenancyTermInMonths();

        // Get application details such as applicant name and application reference number
        $application = $this->getReferencingApplication($this->linkRef);
        $applicantName = $application->getFullName();
        $applicationReferenceNumber = $application->getReferenceNumber();

        // Get financial referee details such as company name
        $financialReferee = $this->getReferencingFinancialReferee($this->linkRef);
        $companyName = $financialReferee->getCompanyName();
        $financialReference = $financialReferee->getPayrollNumber();

        $form = $this->getFormFactory()->create(
            new RootStepType(new ContractEmployerRefereeResponseType()),
            null,
            array(
                'stepTypeOptions' => array('tenancyTerm' => $tenancyTerm),
                'removeBack' => true,
                'removeNext' => true,
                'removeSubmit' => false,
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {
            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                // Get form data and add link reference into the array
                $formData = array_merge(
                    $form->get('step')->getData(),
                    array('linkRef' => $this->linkRef)
                );

                // Non-bijective last-minute data transforms
                // todo: Fix!
                $formData['employmentStartDate'] = $this->transformDateTime($formData['employmentStartDate']);
                $formData['employmentEndDate'] = $this->transformDateTime($formData['employmentEndDate']);

                // Add IP address to form data
                $formData['refereeIpAddress'] = $this->getSymfonyRequest()->getClientIp();

                // Store form content
                $this->getIrisSystemContext()->getSystemApplicationClient()->submitContract(
                    $formData
                );

                // Show generic thank you message
                $this->_helper->redirector->gotoUrl('/referee/completed');

            }
        }

        $this->renderTwigView('/iris-referee/generic-step.html.twig', array(
            'bodyTitle' => 'Contract Employer Referee',
            'applicantName' => $applicantName,
            'applicationReferenceNumber' => $applicationReferenceNumber,
            'companyName' => $companyName,
            'financialReference' => $financialReference,
            'formTheme' => 'referee-widgets.html.twig',
            'form' => $form->createView(),
        ));
    }

    /**
     * Self employed referee action
     *
     * @return void
     */
    public function selfEmployedRefereeAction()
    {
        // Get application details such as applicant name and application reference number
        $application = $this->getReferencingApplication($this->linkRef);
        $applicantName = $application->getFullName();
        $applicationReferenceNumber = $application->getReferenceNumber();

        // Get financial referee details such as accountant name
        $financialReferee = $this->getReferencingFinancialReferee($this->linkRef);
        $accountantName = $financialReferee->getCompanyName();

        $form = $this->getFormFactory()->create(
            new RootStepType(new SelfEmployedRefereeResponseType()),
            null,
            array(
                'removeBack' => true,
                'removeNext' => true,
                'removeSubmit' => false,
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                // Get form data and add link reference into the array
                $formData = array_merge(
                    $form->get('step')->getData(),
                    array('linkRef' => $this->linkRef)
                );

                // Add IP address to form data
                $formData['refereeIpAddress'] = $this->getSymfonyRequest()->getClientIp();

                // Store form content
                $this->getIrisSystemContext()->getSystemApplicationClient()->submitSelfEmployed(
                    $formData
                );

                // Show generic thank you message
                $this->_helper->redirector->gotoUrl('/referee/completed');

            }
        }

        $this->renderTwigView('self-employed-referee.html.twig', array(
            'bodyTitle' => 'Self Employed Referee',
            'applicantName' => $applicantName,
            'applicationReferenceNumber' => $applicationReferenceNumber,
            'accountantName' => $accountantName,
            'formTheme' => 'referee-widgets.html.twig',
            'form' => $form->createView(),
        ));
    }

    /**
     * Pension provider referee action
     *
     * @return void
     */
    public function pensionProviderRefereeAction()
    {
        // Get application details such as applicant name and application reference number
        $application = $this->getReferencingApplication($this->linkRef);
        $applicantName = $application->getFullName();
        $applicationReferenceNumber = $application->getReferenceNumber();

        // Get financial referee details such as pension provider name
        $financialReferee = $this->getReferencingFinancialReferee($this->linkRef);
        $pensionProviderName = $financialReferee->getCompanyName();
        $financialReference = $financialReferee->getPayrollNumber();

        $form = $this->getFormFactory()->create(
            new RootStepType(new PensionProviderRefereeResponseType()),
            null,
            array(
                'removeBack' => true,
                'removeNext' => true,
                'removeSubmit' => false,
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {
            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                // Get form data and add link reference into the array
                $formData = array_merge(
                    $form->get('step')->getData(),
                    array('linkRef' => $this->linkRef)
                );

                // Add IP address to form data
                $formData['refereeIpAddress'] = $this->getSymfonyRequest()->getClientIp();

                // Store form content
                $this->getIrisSystemContext()->getSystemApplicationClient()->submitRetired(
                    $formData
                );

                // Show generic thank you message
                $this->_helper->redirector->gotoUrl('/referee/completed');

            }
        }

        $this->renderTwigView('pension-provider-referee.html.twig', array(
            'bodyTitle' => 'Pension Provider Referee',
            'applicantName' => $applicantName,
            'applicationReferenceNumber' => $applicationReferenceNumber,
            'pensionProviderName' => $pensionProviderName,
            'financialReference' => $financialReference,
            'formTheme' => 'referee-widgets.html.twig',
            'form' => $form->createView(),
        ));
    }

    /**
     * Employer referee action
     *
     * @return void
     */
    public function employerRefereeAction()
    {
        // Get length of tenancy term from the case to show in form question
        $case = $this->getReferencingCase($this->linkRef);
        $tenancyTerm = $case->getTenancyTermInMonths();

        // Get application details such as applicant name and application reference number
        $application = $this->getReferencingApplication($this->linkRef);
        $applicantName = $application->getFullName();
        $applicationReferenceNumber = $application->getReferenceNumber();

        // Get financial referee details such as company name and payroll/NI number
        $financialReferee = $this->getReferencingFinancialReferee($this->linkRef);
        $companyName = $financialReferee->getCompanyName();
        $financialReference = $financialReferee->getPayrollNumber();

        $form = $this->getFormFactory()->create(
            new RootStepType(new EmployerRefereeResponseType()),
            null,
            array(
                'stepTypeOptions' => array('tenancyTerm' => $tenancyTerm),
                'removeBack' => true,
                'removeNext' => true,
                'removeSubmit' => false,
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {
            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                // Get form data and add link reference into the array
                $formData = array_merge(
                    $form->get('step')->getData(),
                    array('linkRef' => $this->linkRef)
                );

                // Non-bijective last-minute data transforms
                // todo: Fix!
                // todo: maybe use model transformer in form type?
                $formData['employmentStartDate'] = $this->transformDateTime($formData['employmentStartDate']);
                $formData['employmentEndDate'] = $this->transformDateTime($formData['employmentEndDate']);

                // Add IP address to form data
                $formData['refereeIpAddress'] = $this->getSymfonyRequest()->getClientIp();

                // Store form content
                $this->getIrisSystemContext()->getSystemApplicationClient()->submitEmployed(
                    $formData
                );

                // Show generic thank you message
                $this->_helper->redirector->gotoUrl('/referee/completed');

            }
        }

        $this->renderTwigView('/iris-referee/generic-step.html.twig', array(
            'bodyTitle' => 'Employer Referee',
            'applicantName' => $applicantName,
            'applicationReferenceNumber' => $applicationReferenceNumber,
            'companyName' => $companyName,
            'financialReference' => $financialReference,
            'formTheme' => 'referee-widgets.html.twig',
            'form' => $form->createView(),
        ));
    }

    /**
     * Completed action - displays a simple "thank you" once any of the other actions have successfully stored
     * user-submitted data
     *
     * @return void
     */
    public function completedAction()
    {
        $this->renderTwigView('/iris-referee/completed.html.twig', array(
            'bodyTitle' => 'Details Updated',
        ));
    }

    /**
     * Get referencing application using link ref
     *
     * @param string $linkRef
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication
     */
    private function getReferencingApplication($linkRef)
    {
        return $this
            ->getIrisSystemContext()
            ->getSystemApplicationClient()
            ->getReferencingApplication(array(
                'linkRef' => $linkRef,
            ))
        ;
    }

    /**
     * Get referencing case from link ref
     *
     * @param string $linkRef
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase
     */
    private function getReferencingCase($linkRef)
    {
        $application = $this->getReferencingApplication($linkRef);

        return $this
            ->getIrisSystemContext()
            ->getSystemApplicationClient()
            ->getReferencingCase(array(
                'referencingCaseUuId' => $application->getReferencingCaseUuId(),
            ))
        ;
    }

    /**
     * Get financial referee from link ref
     *
     * @param string $linkRef
     * @return \Barbondev\IRISSDK\Common\Model\FinancialReferee
     */
    private function getReferencingFinancialReferee($linkRef)
    {
        return $this
            ->getIrisSystemContext()
            ->getSystemApplicationClient()
            ->getReferencingFinancialReferee(array(
                'linkRef' => $linkRef,
            ))
        ;
    }
}