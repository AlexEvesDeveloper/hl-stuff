<?php

namespace Barbondev\IRISSDK\SystemApplication\SystemApplication;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Guzzle\Http\Mimetypes;

/**
 * Class SystemApplicationClient
 *
 * @package Barbondev\IRISSDK\SystemApplication\SystemApplication
 * @author Paul Swift <paul.swift@barbon.com>
 *
 * @method \Guzzle\Http\Message\Response paymentStatus(array $args = array())
 * @method \Guzzle\Http\Message\Response paymentOrder(array $args = array())
 * @method \Guzzle\Http\Message\Response validateLink(array $args = array())
 * @method \Guzzle\Http\Message\Response validateReviewLink(array $args = array())
 * @method \Guzzle\Http\Message\Response submitApplication(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication getReferencingApplication(array $args = array())
 * @method \Barbondev\IRISSDK\Common\Model\FinancialReferee getReferencingFinancialReferee(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase getReferencingCase(array $args = array())
 * @method \Guzzle\Http\Message\Response submitContract(array $args = array())
 * @method \Guzzle\Http\Message\Response submitSelfEmployed(array $args = array())
 * @method \Guzzle\Http\Message\Response submitEmployed(array $args = array())
 * @method \Guzzle\Http\Message\Response submitRetired(array $args = array())
 * @method \Guzzle\Http\Message\Response submitLetting(array $args = array())
 * @method \Guzzle\Http\Message\Response updateApplicant(array $args = array())
 * @method \Barbondev\IRISSDK\SystemApplication\SystemApplication\Model\ReferencingApplicationFindResults findReferencingApplications(array $args = array())
 * @method \Guzzle\Common\Collection getDocuments(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\Note\Model\Note createReferencingApplicationNote(array $args = array())
 * @method \Guzzle\Http\Message\Response updateReferencingApplicationNote(array $args = array())
 * @method \Guzzle\Http\Message\Response getAgentBranch(array $args = array())
 *
 */
class SystemApplicationClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return SystemApplicationClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/systemapplication-systemapplication-v%s.php',
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
            sprintf('/referencing/v1/system/application/%s/document/upload', $args['referencingApplicationUuId'])
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
