<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Guzzle\Http\Mimetypes;

/**
 * Class ReferencingApplicationClient
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication createReferencingApplication(array $args = array())
 * @method \Guzzle\Http\Message\Response updateReferencingApplication(array $args = array())
 * @method \Guzzle\Http\Message\Response cancelReferencingApplication(array $args = array())
 * @method \Guzzle\Http\Message\Response submitReferencingApplication(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Progress getProgress(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication getReferencingApplication(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication createReferencingGuarantorApplication(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplicationFindResults findReferencingApplications(array $args = array())
 * @method \Guzzle\Common\Collection getDocuments(array $args = array())
 * @method \Guzzle\Http\Message\Response resendCompletionEmailToApplicant(array $args = array())
 * @method \Guzzle\Common\Collection getReportNotifications(array $args = array())
 * @method \Guzzle\Http\Message\Response updateReferencingApplicationEmail(array $args = array())
 */
class ReferencingApplicationClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return ReferencingApplicationClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/referencing-application-v%s.php',
            ))
            ->build()
        ;
    }

    /**
     * Upload a new document to an application
     *
     * Example arguments
     * <code>
     * Array (
     *     applicationUuId: 36481551-45bd-1b81-8145-c20c6a260004
     *     file: /tmp/file
     *     fileName: document.pdf
     *     description: My lovely document
     *     categoryId: 1
     * )
     * </code>
     *
     * For categoryId, please refer to @see Barbondev\IRISSDK\Common\EnumerationDocumentCategoryOptions
     *
     * @param array $args
     * @return \Guzzle\Http\Message\Response
     */
    public function uploadDocument(array $args = array())
    {
        $request = $this->post(
            sprintf('/referencing/v1/individual/application/%s/document/upload', $args['referencingApplicationUuId'])
        );

        $request->setPostField('fileName', $args['fileName']);
        $request->setPostField('description', $args['description']);
        $request->setPostField('category', $args['categoryId']);

        $request->addPostFile(
            'file',
            $args['file'],
            Mimetypes::getInstance()->fromFilename(basename($args['file']))
        );

        return $request->send();
    }
}