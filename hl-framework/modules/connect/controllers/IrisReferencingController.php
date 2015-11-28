<?php

require_once __DIR__ . '/IrisConnectAbstractController.php';

use Iris\Utility\RentAffordability\Form\Type\RentAffordabilityType;
use Iris\IndividualApplication\Form\SearchIndividualApplicationsType;
use Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability;
use Guzzle\Service\Exception\ValidationException;
use Iris\IndividualApplication\Search\IndividualApplicationSearch;
use Iris\Utility\Pagination\Paginator;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Document;
use Barbondev\IRISSDK\Utility\Document\Model\Document as DocumentFile;
use Iris\IndividualApplication\Form\NoteType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Barbondev\IRISSDK\Common\Enumeration\DocumentCategoryOptions;
use Iris\Utility\Product\Exception\ProductNotFoundException;
use Iris\Utility\Product\Exception\ProductPriceNotFoundException;
use Barbondev\IRISSDK\Common\Exception\DefaultException;
use Iris\Referencing\Form\Type\UploadDeclarationType;
use Iris\Common\UploadableFileConstraintParameters;
use Iris\IndividualApplication\Form\UpdateApplicantEmailType;

/**
 * Class Connect_IrisReferencingController
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Connect_IrisReferencingController extends IrisConnectAbstractController
{
    /**
     * Report types
     */
    const REPORT_TYPE_INTERIM = 'interim';
    const REPORT_TYPE_FINAL = 'final';

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        /** @var \Desarrolla2\Cache\Cache $cache */
        $cache = $this->getContainer()->get('iris.notifications_cache');
        $cacheKey = sha1($this->_agentSchemeNumber . 'notifications');

        // Get notifications (from cache is available)
        if ($cache->has($cacheKey)) {
            $reportNotifications = $cache->get($cacheKey);
        }
        else {
            try {
                $reportNotifications = $this
                    ->getIrisAgentContext()
                    ->getReferencingApplicationClient()
                    ->getReportNotifications()
                ;
            }
            catch (DefaultException $e) {
                $reportNotifications = array();
            }
            $cache->set($cacheKey, $reportNotifications);
        }

        $agent = new Datasource_Core_AgentUser();
        $canDisplayCheckRight = $agent->canDisplayCheckRight($this->_agentSchemeNumber, $this->_agentUserName);

        $this->renderTwigView('/iris-referencing/index.html.twig', array(
            'rentAffordabilityForm' => $this->_getRentAffordabilityForm()->createView(),
            'reportNotifications' => $reportNotifications,
            'canDisplayCheckRight' => $canDisplayCheckRight,
            'agentSchemeNumber' => $this->_agentSchemeNumber,
            'disabledNewReferenceList' => array(
                1508996,  // test agent
                1506765,  // Burchell Edwards Lettings (Burton on Trent)
                1507004,  // Burchell Edwards Lettings (Solihull)
                1507005,  // Burchell Edwards Lettings (Sheldon)
                1507006,  // Burchell Edwards Lettings (Erdington)
                1507007,  // Burchell Edwards Lettings (Tamworth)
                1507008,  // Burchell Edwards Lettings (Lichfield)
                1507009,  // Ashley Adams (Derby)
                1507010,  // Burchell Edwards Lettings (Belper)
                1507011,  // Burchell Edwards Lettings (Ripley)
                1507012,  // Burchell Edwards Lettings (Ilkeston)
                1507013,  // Burchell Edwards Lettings (Eastwood)
                1507014,  // Burchell Edwards Lettings (Mansfield)
                1508250,  // Burchell Edwards Ltd (Castle Bromwich)
                1509271,  // Burchell Edwards (Shirley)
                1510027,  // Knight Partnership Lettings Limited
                1512889,  // Burchell Edwards Lettings (Hucknall)
                1507015,  // Burchell Edwards Lettings (Property Management Centre)
                1507016,  // Burchell Edwards Lettings (Call Centre)
                1508284,  // Burchell Edwards (Leicester)
                1513311,  // Connells (Hampton Vale)
                1513353,  // Connells (Blaby)
                1513354,  // Connells (Grantham)
                1513355,  // Connells (Kettering)
                1513356,  // Connells (Leicester)
                1513357,  // Connells (Market Harborough)
                1513358,  // Connells (Melton Mowbray)
                1513359,  // Connells (Oadby)
                1513360,  // Connells (Oakley Vale)
                1513361,  // Connells (Peterborough)
            ),
        ));
    }

    /**
     * Message that is displayed when the agent is unable to perform referencing
     * procedures (e.g. new reference). Usually because they have no products available. @see
     * src/main/php/application/modules/connect/controllers/helpers/Auth.php (Line 156)
     *
     * @return void
     */
    public function cannotPerformReferencingErrorAction()
    {
        $this->renderTwigView('/iris-referencing/cannot-perform-referencing-error.html.twig');
    }

    /**
     * Search references
     *
     * @return void
     */
    public function searchReferencesAction()
    {
        $this->renderTwigView('/iris-referencing/search-references.html.twig', array(
            'form' => $this->_getSearchIndividualApplicationsForm()->createView(),
        ));
    }

    /**
     * Submission error action
     *
     * @return void
     */
    public function submissionErrorAction()
    {
        $failureReason = $this
            ->getContainer()
            ->get('iris.referencing.submission.submission_failure_message_resolver')
            ->getFailureMessage($this->getSymfonyRequest()->query->get('errorCode'))
        ;

        $this->renderTwigView('/iris-referencing/submission-error.html.twig', array(
            'reasonForFailure' => $failureReason,
        ));
    }

    /**
     * Product help action
     *
     * @throws Zend_Exception
     * @return void
     */
    public function productHelpAction()
    {
        $errors = array();

        // Turn off layout and renderer
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        // Get the agent progressive store to get letting type and offering type
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
        $case = \Zend_Registry::get('iris_container')
            ->get('iris.referencing.form_set.progressive_store.agent_progressive_store')
            ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase')
        ;

        $productId = $this->getSymfonyRequest()->query->get('productId', null);
        if (null === $productId || !$productId) {
            $errors[] = 'Please select a product';
        }

        $policyLengthInMonths = 0;
        if ($this->getSymfonyRequest()->query->has('policyLengthInMonths')) {

            $policyLengthInMonths = $this->getSymfonyRequest()->query->get('policyLengthInMonths', null);

            if (is_numeric($policyLengthInMonths) && $policyLengthInMonths > 0) {
                if ((6 != $policyLengthInMonths) && (12 != $policyLengthInMonths)) {
                    $errors[] = 'Policy length is invalid';
                }
                // Set policy lengths for international products (only 6 months)
                if ((17 == $productId) || (18 == $productId)) {
                    $policyLengthInMonths = 6;
                }
            }
            elseif (null === $policyLengthInMonths || '' == $policyLengthInMonths) {
                $errors[] = 'Please select a policy length';
            }
        }

        $shareOfRent = $this->getSymfonyRequest()->query->get('shareOfRent', null);
        if (null === $shareOfRent || !$shareOfRent) {
            $errors[] = 'Please enter the share of rent';
        }

        // Get product
        $product = null;
        try {
            $product = \Zend_Registry::get('iris_container')
                ->get('iris.product')
                ->getProduct($case->getRentGuaranteeOfferingType(), $case->getPropertyLetType(), $productId)
            ;
        }
        catch (ProductNotFoundException $e) {
            $errors[] = 'Product could not be found';
        }

        $price = null;
        if (!count($errors)) {

            // Get prices
            try {
                $price = \Zend_Registry::get('iris_container')
                    ->get('iris.product_price')
                    ->getProductPrice(
                        $this->_agentSchemeNumber,
                        $productId,
                        $case->getPropertyLetType(),
                        $case->getRentGuaranteeOfferingType(),
                        $shareOfRent,
                        $policyLengthInMonths
                    )
                ;
            }
            catch (ProductPriceNotFoundException $e) {
                $errors[] = 'Product price could not be found';
            }
        }

        $this->renderTwigView('/iris-referencing/product-help.html.twig', array(
            'popupTitle' => 'Product Help',
            'product' => $product,
            'price' => $price,
            'policyLengthInMonths' => $policyLengthInMonths,
            'shareOfRent' => $shareOfRent,
            'errors' => $errors,
        ));
    }

    /**
     * Find addresses
     *
     * @return void
     */
    public function findAddressesAction()
    {
        // Turn off layout and renderer
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        /** @var \Iris\Utility\AddressFinder\AddressFinder $addressFinder */
        $addressFinder = $this->getContainer()->get('iris.address_finder');

        $postcode = $this->getSymfonyRequest()->query->get('postcode');
        $addresses = $addressFinder->find($postcode);

        $addressesJson = array();

        /** @var \Iris\Utility\AddressFinder\Model\Address $address */
        foreach ($addresses as $address) {
            $addressesJson[] = $address->toArray();
        }

        $this
            ->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
        ;

        $this->getResponse()->appendBody(json_encode($addressesJson));
    }

    /**
     * Resend completion prompt email to tenant action
     *
     * @return void
     */
    public function resendCompletionEmailToTenantAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');

        $isSuccess = null;

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $form = $this
            ->getFormFactory()
            ->create(
                new UpdateApplicantEmailType(),
                $application
            )
        ;

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                // Update applicant's email address
                $this
                    ->getIrisClientRegistry()
                    ->getAgentContext()
                    ->getReferencingApplicationClient()
                    ->updateReferencingApplicationEmail(array(
                        'referencingApplicationUuId' => $applicationUuId,
                        'email' => $form->getData()->getEmail(),
                    ))
                ;

                // Rest before subsequent REST call
                sleep(1);

                // Resed email to applicant
                $response = $this
                    ->getIrisAgentContext()
                    ->getReferencingApplicationClient()
                    ->resendCompletionEmailToApplicant(array(
                        'referencingApplicationUuId' => $applicationUuId,
                    ))
                ;

                $isSuccess = (200 == $response->getStatusCode());
            }
        }

        $this->renderTwigView('/iris-referencing/resend-tenant-completion-email.html.twig', array(
            'isSuccess' => $isSuccess,
            'application' => $application,
            'form' => $form->createView(),
            'isValid' => $form->isValid(),
        ));
    }

    /**
     * Search references results
     *
     * @return void
     */
    public function searchReferencesResultsAction()
    {
        $form = $this->_getSearchIndividualApplicationsForm();

        $form->handleRequest($this->getSymfonyRequest());

        if ($form->isValid()) {

            /** @var \Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria $criteria */
            $criteria = $form->getData();
            $currentAgentSchemeNumber = $this->_agentSchemeNumber;

            $paginator = new Paginator();

            $search = $this->getContainer()->get('iris.reference_search');

            $pagination = $paginator->paginate(
                function ($offset, $limit) use ($search, $criteria, $currentAgentSchemeNumber) {
                    return $search
                        ->search($currentAgentSchemeNumber, $criteria, $offset, $limit)
                    ;
                },
                $this->getSymfonyRequest()->query->get('page', 1),
                $criteria->getResultsPerPage() ?: null,
                5,
                $this->getSymfonyRequest()->getQueryString()
            );
        }

        $this->renderTwigView('/iris-referencing/search-references-results.html.twig', array(
            'form' => $form->createView(),
            'pagination' => $pagination,
        ));
    }

    /**
     * Reference Summary action
     *
     * @return void
     */
    public function summaryAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');

        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $case = $this
            ->getIrisAgentContext()
            ->getReferencingCaseClient()
            ->getReferencingCase(array(
                'referencingCaseUuId' => $application->getReferencingCaseUuId(),
            ))
        ;

        $progress = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getProgress(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $caseApplications = $this
            ->getIrisAgentContext()
            ->getReferencingCaseClient()
            ->getApplications(array(
                'referencingCaseUuId' => $application->getReferencingCaseUuId(),
            ))
        ;

        $this->renderTwigView('/iris-referencing/summary.html.twig', array(
            'application' => $application,
            'case' => $case,
            'progress' => $progress,
            'caseApplications' => $caseApplications,
        ));
    }

    /**
     * Application case log action
     *
     * @return void
     */
    public function caseLogAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');

        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $entries = array();

        $activities = $this
            ->getIrisAgentContext()
            ->getActivityClient()
            ->getActivities(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $notes = $this
            ->getIrisAgentContext()
            ->getNoteClient()
            ->getReferencingApplicationNotes(array(
                'applicationUuId' => $applicationUuId,
            ))
        ;

        $i = 0;

        foreach ($activities as $activity) {
            $entries[sprintf('%s-activity-%d', date('U', strtotime($activity->getRecordedAt())), $i)] = array(
                'content' => $activity->getNote(),
                'recordedAt' => $activity->getRecordedAt(),
                'type' => 'activity',
            );
            $i ++;
        }

        foreach ($notes as $note) {
            $entries[sprintf('%s-note-%d', date('U', strtotime($note->getRecordedAt())), $i)] = array(
                'content' => $note->getNote(),
                'recordedAt' => $note->getRecordedAt(),
                'createdBy' => $note->getCreatedBy(),
                'creatorType' => $note->getCreatorType(),
                'type' => 'note',
            );
            $i ++;
        }

        $this->knatsort($entries);

        $this->renderTwigView('/iris-referencing/case-log.html.twig', array(
            'application' => $application,
            'entries' => $entries,
        ));
    }

    /**
     * Natural sort by array key
     * @todo lift this out into a service
     *
     * @param array $karr
     * @return void
     */
    private function knatsort(&$karr)
    {
        $kkeyarr = array_keys($karr);
        natsort($kkeyarr);
        $ksortedarr = array();

        foreach($kkeyarr as $kcurrkey){
            $ksortedarr[$kcurrkey] = $karr[$kcurrkey];
        }

        $karr = $ksortedarr;
    }

    /**
     * Email assessor function
     *
     * @return void
     */
    public function emailAssessorAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');

        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $form = $this->getFormFactory()->create(new NoteType());
        $noteAddSuccess = null;

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            if ($form->isValid()) {

                $data = $form->getData();

                // Upload attachment
                $attachment = $data['attachment'];
                $fileUploadSuccess = null;
                $fileName = '';
                if ($attachment instanceof UploadedFile) {

                    $fileName = $attachment->getClientOriginalName();

                    $fileName = rtrim($fileName, $attachment->getClientOriginalExtension());

                    $fileName = $this->getSlugifier()->slugify($fileName, '_') . '.' .
                        strtolower($attachment->getClientOriginalExtension());

                    $result = $this
                        ->getIrisAgentContext()
                        ->getReferencingApplicationClient()
                        ->uploadDocument(array(
                            'referencingApplicationUuId' => $applicationUuId,
                            'file' => $attachment->getPathname(),
                            'fileName' => $fileName,
                            'description' => 'Email Assessor Attachment',
                            'categoryId' => DocumentCategoryOptions::MISCELLANEOUS,
                        ))
                    ;

                    $fileUploadSuccess = in_array($result->getStatusCode(), array(200, 201));
                }

                if (null === $fileUploadSuccess || true === $fileUploadSuccess) {

                    // Append attachment message to bottom of note
                    if (true === $fileUploadSuccess) {
                        $data['note'] .= "\n\n [Attachment: {$fileName}]";
                    }

                    $noteAddSuccess = true;

                    try {
                        $this
                            ->getIrisAgentContext()
                            ->getNoteClient()
                            ->createReferencingApplicationNote(array(
                                'applicationUuId' => $applicationUuId,
                                'note' => $data['note'],
                                'emailAssesor' => true,
                            ))
                        ;
                    }
                    catch (DefaultException $e) {
                        $noteAddSuccess = false;
                    }
                }
                else {

                    // File failed to upload
                    $noteAddSuccess = false;
                }
            }
        }

        $this->renderTwigView('/iris-referencing/email-assessor.html.twig', array(
            'application' => $application,
            'form' => $form->createView(),
            'success' => $noteAddSuccess,
        ));
    }

    /**
     * New reference action
     *
     * @return void
     */
    public function applicationFormAction()
    {
        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $this->getSymfonyRequest()->query->get('uuid'),
            ))
        ;

        $case = $this
            ->getIrisAgentContext()
            ->getReferencingCaseClient()
            ->getReferencingCase(array(
                'referencingCaseUuId' => $application->getReferencingCaseUuId(),
            ))
        ;

        $template = '/iris-referencing/application-form.html.twig';

        // If guarantor, display guarantor template version
        if (2 == $application->getApplicationType()) {
            $template = '/iris-referencing/application-form-guarantor.html.twig';
        }

        $this->renderTwigView($template, array(
            'application' => $application,
            'case' => $case,
        ));
    }

    /**
     * View interim report
     *
     * @return void
     */
    public function viewInterimReportAction()
    {
        if ($this->getSymfonyRequest()->query->has('linkref')) {
            $applicationUuId = $this->getSymfonyRequest()->query->get('linkref');
        }
        else {
            $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');
        }

        $this->_helper->redirector->gotoUrlAndExit(
            sprintf('/iris-referencing/view-report?uuid=%s&reportType=interim', $applicationUuId)
        );
    }

    /**
     * View final report
     *
     * @return void
     */
    public function viewFinalReportAction()
    {
        if ($this->getSymfonyRequest()->query->has('linkref')) {
            $applicationUuId = $this->getSymfonyRequest()->query->get('linkref');
        }
        else {
            $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');
        }

        $this->_helper->redirector->gotoUrlAndExit(
            sprintf('/iris-referencing/view-report?uuid=%s&reportType=final', $applicationUuId)
        );
    }

    /**
     * View report action
     *
     * @return void
     */
    public function viewReportAction()
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $this->getSymfonyRequest()->query->get('uuid'),
            ))
        ;

        $this->renderTwigView('/iris-referencing/view-report.html.twig', array(
            'referencingApplicationUuId' => $application->getReferencingApplicationUuId(),
            'referenceNumber' => $application->getReferenceNumber(),
        ));
    }

    /**
     * Inline report action
     *
     * @return void
     */
    public function inlineReportAction()
    {
        list ($reportDocumentNodeId, $filename) = $this->getReportDocumentNodeIdAndFileName(
            $this->getSymfonyRequest()->query->get('reportType')
        );

        if ($reportDocumentNodeId) {

            // Try to fetch document
            $document = $this
                ->getIrisAgentContext()
                ->getDocumentClient()
                ->getDocument(array(
                    'documentUuId' => $reportDocumentNodeId,
                ))
            ;

            if ($document instanceof DocumentFile) {

                // Turn off layout and renderer
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->getHelper('layout')->disableLayout();

                // Present document as an attachment
                $this->configureResponseForInline($filename);

                $this->getResponse()->appendBody($document->getData());
                return;
            }
        }

        $this->renderTwigView('/iris-referencing/view-report-error.html.twig');
    }

    /**
     * Download report action
     *
     * @return void
     */
    public function downloadReportAction()
    {
        list ($reportDocumentNodeId, $filename) = $this->getReportDocumentNodeIdAndFileName(
            $this->getSymfonyRequest()->query->get('reportType')
        );

        if ($reportDocumentNodeId) {

            // Try to fetch document
            $document = $this
                ->getIrisAgentContext()
                ->getDocumentClient()
                ->getDocument(array(
                    'documentUuId' => $reportDocumentNodeId,
                ))
            ;

            if ($document instanceof DocumentFile) {

                // Turn off layout and renderer
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->getHelper('layout')->disableLayout();

                // Present document as an attachment
                $this->configureResponseForDownload($filename);

                $this->getResponse()->appendBody($document->getData());
                return;
            }
        }

        $this->renderTwigView('/iris-referencing/view-report-error.html.twig');
    }

    /**
     * Get the report document node ID and file name from the request
     *
     * @param $type
     * @throws Zend_Exception
     * @return string
     */
    private function getReportDocumentNodeIdAndFileName($type)
    {
        if ((self::REPORT_TYPE_FINAL != $type) && (self::REPORT_TYPE_INTERIM != $type)) {
            throw new \Zend_Exception(sprintf('Report type "%s" is invalid', $type));
        }

        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid');
        $filename = '';

        // Discover the document node id using the application uuid
        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ));

        $finalReportNodeId = $this->_getReportFinder()->getFinalReportDocumentNodeId($application->getReferencingApplicationUuId());
        $interimReportNodeId = $this->_getReportFinder()->getInterimReportDocumentNodeId($application->getReferencingApplicationUuId());

        $reportDocumentNodeId = null;

        if (self::REPORT_TYPE_FINAL == $type) {
            $reportDocumentNodeId = $finalReportNodeId;
            $filename = $this->_getReportFilenameBuilder()->build('Final', $application->getReferenceNumber(), 'pdf');
        }
        elseif (self::REPORT_TYPE_INTERIM == $type) {
            $reportDocumentNodeId = $interimReportNodeId;
            $filename = $this->_getReportFilenameBuilder()->build('Interim', $application->getReferenceNumber(), 'pdf');
        }

        return array(
            $reportDocumentNodeId,
            $filename,
        );
    }

    /**
     * Configure the response for download
     *
     * @param string $filename
     * @return void
     */
    private function configureResponseForDownload($filename)
    {
        // For some reason, Zend won't allow me to remove these headers
        header('Pragma: ');
        header('Cache-Control: ');

        $this
            ->getResponse()
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', sprintf('attachment; filename="%s"', $filename))
        ;
    }

    /**
     * Configure the response for inline
     *
     * @param string $filename
     * @return void
     */
    private function configureResponseForInline($filename)
    {
        // For some reason, Zend won't allow me to remove these headers
        header('Pragma: ');
        header('Cache-Control: ');

        $this
            ->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
        ;
    }

    /**
     * Upload declaration action
     *
     * @return void
     */
    public function uploadDeclarationAction()
    {
        $isPopup = $this->getSymfonyRequest()->query->get('isPopup');

        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $this->getSymfonyRequest()->query->get('uuid'),
            ))
        ;

        $form = $this->getFormFactory()->create(new UploadDeclarationType());

        $redirectUrl = sprintf(
            '/iris-referencing/upload-declaration?uuid=%s',
            $application->getReferencingApplicationUuId()
        );

        if ($isPopup) {
            $redirectUrl .= '&isPopup=1';
        }

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->handleRequest($this->getSymfonyRequest());

            if ($form->isValid()) {

                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $declarationFile */
                $declarationFile = $form->get('declaration')->getData();

                $fileName = sprintf(
                    'Signed-Declaration-%s-%s.%s',
                    $application->getReferenceNumber(),
                    date('dmYHis'),
                    strtolower($declarationFile->getClientOriginalExtension())
                );

                try {
                    $this
                        ->getIrisAgentContext()
                        ->getReferencingApplicationClient()
                        ->uploadDocument(array(
                            'referencingApplicationUuId' => $application->getReferencingApplicationUuId(),
                            'fileName' => $fileName,
                            'description' => 'Signed Declaration',
                            'file' => $declarationFile->getPathname(),
                            'categoryId' => DocumentCategoryOptions::MISCELLANEOUS,
                        ))
                    ;
                }
                catch (DefaultException $e) {
                    $this->_helper->redirector->gotoUrlAndExit(
                        sprintf('%s&isSuccess=%d', $redirectUrl, 0)
                    );
                    return;
                }

                $this->_helper->redirector->gotoUrlAndExit(
                    sprintf('%s&isSuccess=%d', $redirectUrl, 1)
                );
                return;
            }
        }

        if ($isPopup) {
            // Turn off layout and renderer
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $this->renderTwigView('/iris-referencing/upload-declaration.html.twig', array(
            'application' => $application,
            'isPopup' => $isPopup,
            'form' => $form->createView(),
            'isSuccess' => $this->getSymfonyRequest()->get('isSuccess', null),
            'maxUploadFileSize' => UploadableFileConstraintParameters::getMaxUploadFileSize(),
        ));
    }

    /**
     * Address finder help
     *
     * @return void
     */
    public function addressFinderHelpAction()
    {
        // Turn off layout and renderer
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        $this->renderTwigView('/iris-referencing/address-finder-help.html.twig');
    }

    /**
     * Rent affordability action
     *
     * @return void
     */
    public function rentAffordabilityAction()
    {
        $isPopup = $this->getSymfonyRequest()->query->has('isPopup');

        $output = new RentAffordability();

        $form = $this->_getRentAffordabilityForm($output);

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->handleRequest($this->getSymfonyRequest());

            if ($form->isValid()) {

                /** @var \Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability $data */
                $data = $form->getData();

                try {
                    if ($data->getMonthlyRent()) {
                        $output = $this
                            ->getIrisAgentContext()
                            ->getRentAffordabilityClient()
                            ->checkRentAffordability(array(
                                'monthlyRent' => $data->getMonthlyRent(),
                            ))
                        ;
                    }
                    elseif ($data->getTenantAnnualIncome()) {
                        $output = $this
                            ->getIrisAgentContext()
                            ->getRentAffordabilityClient()
                            ->checkTenantRentAffordability(array(
                                'income' => $data->getTenantAnnualIncome(),
                            ))
                        ;
                    }
                    elseif ($data->getGuarantorAnnualIncome()) {
                        $output = $this
                            ->getIrisAgentContext()
                            ->getRentAffordabilityClient()
                            ->checkGuarantorRentAffordability(array(
                                'income' => $data->getGuarantorAnnualIncome(),
                            ))
                        ;
                    }
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

            }
        }

        if ($isPopup) {
            // Turn off layout and renderer
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $this->renderTwigView('/iris-referencing/rent-affordability.html.twig', array(
            'form' => $form->createView(),
            'output' => $output,
            'isPopup' => $isPopup,
            'popupTitle' => 'Rent Affordability Calculator',
        ));
    }

    /**
     * Failed to submit application general error
     *
     * @return void
     */
    public function failedToSubmitApplicationAction()
    {
        $application = $this
            ->getIrisAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $this->getSymfonyRequest()->query->get('uuid'),
            ))
        ;

        $this->renderTwigView('/iris-referencing/failed-to-submit-application.html.twig', array(
            'application' => $application,
        ));
    }

    /**
     * Get rent affordability form
     *
     * @param \Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability|null $data
     * @return \Symfony\Component\Form\FormInterface
     */
    private function _getRentAffordabilityForm($data = null)
    {
        return $this->getFormFactory()->create(new RentAffordabilityType(), $data);
    }

    /**
     * Get search individual applications form
     *
     * @param array|null $data
     * @return \Symfony\Component\Form\FormInterface
     */
    private function _getSearchIndividualApplicationsForm($data = null)
    {
        return $this->getFormFactory()->create(new SearchIndividualApplicationsType(), $data);
    }

    /**
     * Get report finder
     *
     * @return \Iris\Referencing\Report\ReportFinder
     */
    private function _getReportFinder()
    {
        return $this->getContainer()->get('iris.referencing.report.agent_report_finder');
    }

    /**
     * Get report filename builder
     *
     * @return \Iris\Referencing\Report\ReportFilenameBuilder
     */
    private function _getReportFilenameBuilder()
    {
        return $this->getContainer()->get('iris.referencing.report.report_filename_builder');
    }

    /**
     * Get slugified service
     *
     * @return \AshleyDawson\Slugify\Slugifier
     */
    private function getSlugifier()
    {
        return $this->getContainer()->get('slugifier');
    }
}
