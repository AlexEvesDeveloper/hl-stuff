<?php

require_once __DIR__ . '/IrisTatAbstractController.php';

use Iris\Referencing\Form\Type\TatCallMeType;
use Symfony\Component\Form\FormInterface;
use Barbondev\IRISSDK\Common\Exception\NotFoundException;
use Barbondev\IRISSDK\SystemApplication\Tat\Model\TatStatus;
use Iris\Referencing\Form\Type\TatLoginType;

/**
 * Class TenantApplicationTracker_IndexController
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
class TenantApplicationTracker_IndexController extends IrisTatAbstractController
{
    /**
     * Enumerations
     * todo: not mapped to API yet - TBL to do
     */
    const COMPLETE = 1;
    const IN_PROGRESS = 2;
    const NOT_APPLICABLE = 3;

    /**
     * Login screen
     *
     * @throws Barbondev\IRISSDK\Common\Exception\AuthenticationException
     * @return void
     */
    public function loginAction()
    {
        // Show and process login screen
        $form = $this->getFormFactory()->create(
            new TatLoginType()
        );

        // Authorize a user coming from the legacy TAT form
        $globalSession = new Zend_Session_Namespace('homelet_global');
        if (isset($globalSession->legacy_tat_login) && is_array($globalSession->legacy_tat_login)) {

            $agentSchemeNumber = $globalSession->legacy_tat_login['letting_agent_asn'];
            $applicationReferenceNumber = $globalSession->legacy_tat_login['tenant_reference_number'];
            $applicantBirthDate = date('Y-m-d', strtotime($globalSession->legacy_tat_login['tenant_dob']));

            $globalSession->legacy_tat_login = null;

            $authSuccess = $this->authorizeTatLogin($agentSchemeNumber, $applicationReferenceNumber, $applicantBirthDate);
            if (true === $authSuccess) {
                $this->_helper->redirector->gotoUrl(IrisTatAbstractController::BASE_URL);
                return;
            }

            $this->renderLoginTemplate($authSuccess, $form);
            return;
        }

        // Before testing for a POST, check that a link reference is known
        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                $formData = $form->getData();

                $agentSchemeNumber = $formData['agentSchemeNumber'];
                $applicationReferenceNumber = $formData['applicationReferenceNumber'];
                $applicantBirthDate = $formData['applicantBirthDate']->format('Y-m-d');

                $authSuccess = $this->authorizeTatLogin($agentSchemeNumber, $applicationReferenceNumber, $applicantBirthDate);
                if (true === $authSuccess) {
                    $this->_helper->redirector->gotoUrl(IrisTatAbstractController::BASE_URL);
                    return;
                }
            }
        }

        $this->renderLoginTemplate($authSuccess, $form);
    }

    /**
     * Helper method to render the login form template
     *
     * @param string|bool $failureMessage
     * @param FormInterface $form
     * @return void
     */
    private function renderLoginTemplate($failureMessage, FormInterface $form)
    {
        $this->renderTwigView('/iris-tat/login.html.twig', array(
            'bodyTitle' => 'HomeLet Tenant Referencing Application Tracker - Login',
            'formTheme' => 'tat-widgets.html.twig',
            'formMessage' => $failureMessage,
            'form' => $form->createView(),
        ));
    }

    /**
     * Authorize TAT login
     *
     * @param int $agentSchemeNumber
     * @param string $applicationReferenceNumber
     * @param string $applicantBirthDate
     * @return bool|string
     */
    private function authorizeTatLogin($agentSchemeNumber, $applicationReferenceNumber, $applicantBirthDate)
    {
        $formMessage = '';

        // Check link ref and all submitted data is valid via API
        try {
            $response = $this->getIrisSystemContext()->getTatClient()->getTatStatus(
                array(
                    'agentSchemeNumber' => (int) $agentSchemeNumber,
                    'applicationReferenceNumber' => $applicationReferenceNumber,
                    'applicantBirthDate' => $applicantBirthDate,
                )
            );
        }
        catch (NotFoundException $e) {
            // The most common exception will be that a user's details don't match, so rather than bubble this
            //   up we'll give the user a friendly message and let them try again.
            $this->authSession->hasAuth = false;
            //throw new AuthenticationException('Not Found Exception: ' . $e->getMessage());
            $formMessage = 'Unable to log you in - please check all your details are correct and try again.';
        }
        catch (\Exception $e) {
            // Some other exception has been thrown, bubble up and finish here.
            $this->authSession->hasAuth = false;
            //throw new AuthenticationException('Exception: ' . $e->getMessage());
            $formMessage = 'There has been a problem with your login. ' . $e->getMessage();
        }

        // All should be good at this point, ensure it is and store auth details
        if (isset($response) && $response instanceof TatStatus) {

            $this->authSession->hasAuth = true;
            $this->authSession->agentSchemeNumber = $agentSchemeNumber;
            $this->authSession->applicationReferenceNumber = $applicationReferenceNumber;
            $this->authSession->applicantBirthDate = $applicantBirthDate;

            return true;

        } elseif (!$formMessage) {

            // Something has gone very awry and there isn't a soft response to the user - throw an exception and
            //   give up.
            $this->authSession->hasAuth = false;
            //throw new AuthenticationException('Unexpected response type.');
            $formMessage = 'Unable to log you in - Unexpected response type.';
        }

        return $formMessage;
    }

    /**
     * Main TAT screen (protected by login)
     *
     * @return void
     */
    public function indexAction()
    {
        // Get TAT status
        $tatStatus = $this->getIrisSystemContext()->getTatClient()->getTatStatus(array(
            'agentSchemeNumber' => (int) $this->agentSchemeNumber,
            'applicationReferenceNumber' => $this->applicationReferenceNumber,
            'applicantBirthDate' => $this->applicantBirthDate,
        ));

        // Compare summary status ready for display
        $summaryStatus = ('Complete' == $tatStatus->getStatus()) ? self::COMPLETE : self::IN_PROGRESS;

        // Compare expected sub-statuses ready for display
        $incomeStatus = self::NOT_APPLICABLE;
        $additionalIncomeStatus = self::NOT_APPLICABLE;
        $futureIncomeStatus = self::NOT_APPLICABLE;
        $landlordStatus = self::NOT_APPLICABLE;

        if ($tatStatus->getIncomeStatus() != 'N/A') {
            $incomeStatus = ('Completed' == $tatStatus->getIncomeStatus()) ? self::COMPLETE : self::IN_PROGRESS;
        }
        if ($tatStatus->getAdditionalIncomeStatus() != 'N/A') {
            $additionalIncomeStatus = ('Completed' == $tatStatus->getAdditionalIncomeStatus()) ? self::COMPLETE : self::IN_PROGRESS;
        }
        if ($tatStatus->getFutureIncomeStatus() != 'N/A') {
            $futureIncomeStatus = ('Completed' == $tatStatus->getFutureIncomeStatus()) ? self::COMPLETE : self::IN_PROGRESS;
        }
        if ($tatStatus->getLandlordStatus() != 'N/A') {
            $landlordStatus = ('Completed' == $tatStatus->getLandlordStatus()) ? self::COMPLETE : self::IN_PROGRESS;
        }

        $this->renderTwigView('/iris-tat/index.html.twig', array(
            'bodyTitle' => 'HomeLet Tenant Referencing Application Tracker',
            'tatStatus' => $tatStatus,
            'summaryStatus' => $summaryStatus,
            'incomeStatus' => $incomeStatus,
            'additionalIncomeStatus' => $additionalIncomeStatus,
            'futureIncomeStatus' => $futureIncomeStatus,
            'landlordStatus' => $landlordStatus,
            'agentSchemeNumber'=> $this->agentSchemeNumber,
        ));
    }

    /**
     * Call me form.
     */
    public function callMeAction()
    {
        // Get TAT status
        $tatStatus = $this->getIrisSystemContext()->getTatClient()->getTatStatus(array(
            'agentSchemeNumber' => (int) $this->agentSchemeNumber,
            'applicationReferenceNumber' => $this->applicationReferenceNumber,
            'applicantBirthDate' => $this->applicantBirthDate,
        ));

        // Create new call me form
        $form = $this->getFormFactory()->create(
            new TatCallMeType()
        );

        // Process POSTed form
        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                $formData = $form->getData();

                // Send e-mail to campaign team
                $content  = '';
                $content .= 'Name: ' . $tatStatus->getFirstName() . ' ' . $tatStatus->getLastName() . "\r\n\r\n";
                $content .= "Reference number: {$this->applicationReferenceNumber}\r\n\r\n";
                $content .= "Mobile number: {$formData['mobileNumber']}\r\n\r\n";
                $content .= "Landline number: {$formData['landlineNumber']}\r\n\r\n";
                $content .= "Additional information:\r\n{$formData['additionalInfo']}\r\n\r\n";
                $content .= "Best time to call: {$formData['timeToCall']}\r\n\r\n";
                $content .= "Agent Scheme Number: {$this->agentSchemeNumber}\r\n\r\n";

                $tatMailManager = new Manager_Referencing_TatMail($this->applicationReferenceNumber);
                $tatMailManager->notifyCampaignTeam($content);

                // Show confirmation screen and end here
                $this->renderTwigView('/iris-tat/call-me-sent.html.twig', array(
                    'bodyTitle' => 'HomeLet Tenant\'s Insurance - Message Sent',
                ));

                return;
            }
        }

        $this->renderTwigView('/iris-tat/call-me.html.twig', array(
            'bodyTitle' => 'HomeLet Tenant\'s Insurance',
            'formTheme' => 'tat-widgets.html.twig',
            'tatStatus' => $tatStatus,
            'agentSchemeNumber'=> $this->agentSchemeNumber,
            'form' => $form->createView(),
        ));
    }

    /**
     * Email assessor form.
     *
     * @todo: Make this do something.
     */
    public function emailAction()
    {
        $this->renderTwigView('/iris-tat/email.html.twig', array(

        ));
    }

    /**
     * View previous e-mail correspondence.
     *
     * @todo: Make this do something.
     */
    public function viewEmailsAction()
    {
        $this->renderTwigView('/iris-tat/view-emails.html.twig', array(

        ));
    }
}