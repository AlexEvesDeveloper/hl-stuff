<?php

// Can't be unit tested/code coverage'd as it's all designed to work over AJAX
// @codeCoverageIgnoreStart

/**
 * Note: this is only for AJAX calls that return HTML.  For calls that return
 * data, use the JSON controller.
 */
require_once('ConnectAbstractController.php');
class Connect_AjaxController extends ConnectAbstractController
{
    /**
     * Action for lazy loading the user accounts section of the settings page.
     * This is purely a hack for IE's benefit, it has slow script issues when an
     * agency has a lot of agent users.
     */
    public function settingsUserAccountsAction()
    {
        // Disable layout
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();

        // Generate HTML chunk and send it back.
        echo $this->view->partial(
            'partials/ajax-user-account.phtml',
            array(
                'agentSchemeNumber' => $this->_agentSchemeNumber,
                'agentId'           => $this->_agentId
            )
        );

    }

    public function sendPdfDialogueAction()
    {
        $request = $this->getRequest();
        if (!is_null($request->getParam('filename'))) {

            $this->_helper->layout->setLayout('popup');
            $this->view->filename = $request->getParam('filename');
        }
    }

    public function rgClaimsAddInfoAction()
    {
        // Disable layout
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();

        // Instantiate session and rental payment manager
        $pageSession = new Zend_Session_Namespace('online_claims');
        $manager = new Manager_Insurance_RentGuaranteeClaim_RentalPayment();

        $formData = array(
            'date_due'          => 'N/A',
            'amount_due'        => '0',
            'date_received'     => 'N/A',
            'amount_received'   => '0'
        );

        // Decide what type of entry this is
        if ($_POST['entryType'] == 'due') {

            // Basic data sanitisation
            $formData['date_due']       = preg_replace('/[^\w\/]/', '', $_POST['dateDue']);
            $formData['amount_due']     = preg_replace('/[^\d\.]/', '', $_POST['amountDue']);

            // Instantiate and validate against subform
            $form = new Connect_Form_Subforms_RentGuaranteeClaims_RentalPaymentsDue();
            // Set all "due" fields to be mandatory
            $form->getElement('date_due')->setRequired(true);
            $form->getElement('amount_due')->setRequired(true);

        } else {

            // Basic data sanitisation
            $formData['date_received']      = preg_replace('/[^\w\/]/', '', $_POST['dateReceived']);
            $formData['amount_received']    = preg_replace('/[^\d\.]/', '', $_POST['amountReceived']);

            // Instantiate and validate against subform
            $form = new Connect_Form_Subforms_RentGuaranteeClaims_RentalPaymentsReceived();
            // Set all "received" fields to be mandatory
            $form->getElement('date_received')->setRequired(true);
            $form->getElement('amount_received')->setRequired(true);
        }



        // If valid, do insertion
        $insertIds = array();
        if ($form->isValid($formData)) {

            // Wrap incoming data in object structure
            $incomingDataInner = new stdClass;
            $incomingDataInner->date_due        = $formData['date_due'];
            $incomingDataInner->amount_due      = $formData['amount_due'];
            $incomingDataInner->date_paid       = $formData['date_received'];
            $incomingDataInner->amount_paid     = $formData['amount_received'];
            $incomingData = new stdClass;
            $incomingData->insertRecords = array();
            $incomingData->insertRecords[] = $incomingDataInner;

            // Insert data
            $insertIds = $manager->createRentalPayments(
                $incomingData,
                $pageSession->ClaimReferenceNumber
            );

        }

        // Re-fetch all rental payment data
        $paymentData = $manager->getRentalPayments(
            $pageSession->ClaimReferenceNumber
        );

        // Return HTML chunk representing all entries as a table
        echo $this->view->partial(
            'partials/rent-guarantee-payment-list.phtml',
            array(
                'paymentData'   => $paymentData['data'],
                'insert'        => $insertIds
            )
        );
    }

    public function rgClaimsRemoveInfoAction()
    {
        // Disable layout
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();

        // Instantiate session and rental payment manager
        $pageSession = new Zend_Session_Namespace('online_claims');
        $manager = new Manager_Insurance_RentGuaranteeClaim_RentalPayment();

        // Wrap incoming data in object structure
        $incomingDataInner = new stdClass;
        $incomingDataInner->id = preg_replace('/\D/', '', $_POST['infoId']);
        $incomingData = new stdClass;
        $incomingData->deleteRecords = array();
        $incomingData->deleteRecords[] = $incomingDataInner;

        // Remove info with given ID
        $manager->removeRentalPayments(
            $incomingData,
            $pageSession->ClaimReferenceNumber
        );

        // Re-fetch all rental payment data
        $paymentData = $manager->getRentalPayments(
            $pageSession->ClaimReferenceNumber
        );

        // Return HTML chunk representing all entries as a table
        echo $this->view->partial('partials/rent-guarantee-payment-list.phtml', array('paymentData' => $paymentData['data']));
    }
}
