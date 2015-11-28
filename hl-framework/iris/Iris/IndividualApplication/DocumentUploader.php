<?php

namespace Iris\IndividualApplication;

use Barbondev\IRISSDK\Common\Enumeration\DocumentCategoryOptions;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\ReferencingApplicationClient;
use Barbondev\IRISSDK\SystemApplication\SystemApplication\SystemApplicationClient;
use Closure;
use Iris\IndividualApplication\DocumentUploader\Exception\InvalidDetailType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class DocumentUploader
 *
 * @package Iris\IndividualApplication
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class DocumentUploader
{
    /**
     * @var ReferencingApplicationClient|SystemApplicationClient
     */
    private $clientContext;

    /**
     * @var ReferencingApplication
     */
    private $application;

    /**
     * Constructor
     *
     * @param ReferencingApplicationClient|SystemApplicationClient $clientContext
     * @param ReferencingApplication $application
     */
    public function __construct($clientContext, ReferencingApplication $application)
    {
        $this->clientContext = $clientContext;
        $this->application = $application;
    }

    /**
     * Synchronise with IRIS
     *
     * @param FormInterface $newDocuments
     * @param FormInterface $existingDocuments
     * @param callable $descriptionClosure
     */
    public function sync(FormInterface $newDocuments, /* FormInterface - not yet implemented */ $existingDocuments, Closure $descriptionClosure = null)
    {
//        $this->removeOld($existingDocuments);
        $this->addMissing($newDocuments, $descriptionClosure);
    }

    /**
     * Get slugified service
     *
     * @return \AshleyDawson\Slugify\Slugifier
     */
    private function getSlugifier()
    {
        return \Zend_Registry::get('iris_container')->get('slugifier');
    }

    /**
     * Upload missing files to upstream service
     *
     * @param $clientDocuments
     * @param callable $fileDetailCallback
     * @throws DocumentUploader\Exception\InvalidDetailType
     */
    private function addMissing($clientDocuments, Closure $fileDetailCallback)
    {
//        $upstreamDocuments = $this->clientContext->getDocuments(array(
//            'referencingApplicationUuId' => $this->application->getReferencingApplicationUuId(),
//        ));

        // Look for documents in the upload that aren't in IRIS
        // These should be added to IRIS.
        /** @var FormInterface $clientDocument */
        foreach ($clientDocuments as $clientDocument) {
            // Get the uploaded file
            /** @var UploadedFile $uploadedFile */
            $uploadedFileData = $clientDocument->getData();

            $uploadedFile = $uploadedFileData['fileItem'];

            if ($uploadedFile) {
//                if (!$this->matchUpstreamDocument($uploadedFile->getClientOriginalName(), $upstreamDocuments)) {
                    // File not found in IRIS, add the file
                    $fileDetail = array();

                    if (null !== $fileDetailCallback) {
                        $fileDetailResponse = $fileDetailCallback(array(
                            'uploadedFile' => $uploadedFile,
                            'application' => $this->application,
                        ));

                        if (null !== $fileDetailResponse && !is_array($fileDetailResponse)) {
                            // Invalid response
                            throw new InvalidDetailType(sprintf('Response of type %s is invalid. Expected null or array.',
                                get_class($fileDetailResponse)));
                        }

                        $fileDetail = $fileDetailResponse;
                    }

                    $fileName = (!empty($fileDetail['uploadedFile']) && is_string($fileDetail['uploadedFile'])) ?
                        $fileDetail['uploadedFile'] : $uploadedFile->getClientOriginalName();

                    $fileName = rtrim($fileName, $uploadedFile->getClientOriginalExtension());

                    $fileName = $this->getSlugifier()->slugify($fileName, '_') . '.' .
                        strtolower($uploadedFile->getClientOriginalExtension());

                    $description = (!empty($fileDetail['description']) && is_string($fileDetail['description']))
                        ? $fileDetail['description'] : '';

                    $this->clientContext->uploadDocument(array(
                        'referencingApplicationUuId' => $this->application->getReferencingApplicationUuId(),

                        'fileName' => $fileName,

                        'description' => $description,

                        'file' => $uploadedFile->getPathname(),

                        // todo: maybe forward this from details builder closure?
                        'categoryId' => DocumentCategoryOptions::MISCELLANEOUS,
                    ));
//                }
            }
        }
    }

//    /**
//     * Remove files removed from the uploaded documents list
//     *
//     * @param $clientDocuments
//     */
//    private function removeOld($clientDocuments)
//    {
//        // TODO: There is currently no facility to remove an uploaded document. Once its uploaded, its there to stay.
//        $upstreamDocuments = $this->clientContext->getDocuments(array(
//            'referencingApplicationUuId' => $this->application->getReferencingApplicationUuId(),
//        ));
//
//        // Look for documents in IRIS that aren't in the upload list.
//        // These should be removed from IRIS.
//        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Document $upstreamDocument */
//        foreach ($upstreamDocuments as $upstreamDocument) {
//            if (!$this->matchLocalDocument($upstreamDocument->getName(), $clientDocuments)) {
//                // TODO: Remove the file from IRIS
//            }
//        }
//    }
//
//    /**
//     * Look for a matching file in the upstream documents
//     *
//     * @param string $uploadedFileName
//     * @param ArrayAccess $upstreamDocuments
//     * @return bool
//     */
//    private function matchUpstreamDocument($uploadedFileName, ArrayAccess $upstreamDocuments)
//    {
//         Import note: match checks for filename only, not that the files are identical,
//         which would require a checksum attribute in the API
//         As the files are managed in two separate lists, we must assume
//         we need to replace all files in the new list even in the use
//         case that the user may have selected an identical file to
//         what they've already uploaded
//        return false;
//
//        // Look for a matching file in IRIS
//        /** @var Document $upstreamDocument */
//        foreach ($upstreamDocuments as $upstreamDocument) {
//            if ($uploadedFileName == $upstreamDocument->getName()) {
//                return true;
//            }
//        }
//
//        return false;
//    }
//
//    /**
//     * Look for a matching file in the local documents
//     *
//     * @param $uploadedFileName
//     * @param ArrayAccess $localDocuments
//     * @return bool
//     */
//    private function matchLocalDocument($uploadedFileName, ArrayAccess $localDocuments)
//    {
//        // Look for a matching file in local file lists
//        foreach ($localDocuments as $localDocument) {
//            if ($uploadedFileName == $localDocument) {
//                return true;
//            }
//        }
//
//        return false;
//    }
}
