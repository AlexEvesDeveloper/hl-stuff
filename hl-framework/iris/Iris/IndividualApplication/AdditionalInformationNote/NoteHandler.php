<?php

namespace Iris\IndividualApplication\AdditionalInformationNote;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\IndividualApplication\Note\Model\Note;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\ProgressiveStore\ProgressiveStoreInterface;
use Iris\Referencing\FormSet\Model\AdditionalInformationHolder;

/**
 * Class NoteHandler
 *
 * @package Iris\IndividualApplication\AdditionalInformationNote
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class NoteHandler
{
    /**
     * Default additional information holder class name
     */
    const MODEL_ADDITIONAL_INFORMATION_HOLDER_CLASS = 'Iris\Referencing\FormSet\Model\AdditionalInformationHolder';

    /**
     * @var AbstractClient
     */
    private $client;

    /**
     * @var ProgressiveStoreInterface
     */
    private $progressiveStore;

    /**
     * @var string
     */
    private $additionalInformationHolderClass;

    /**
     * Constructor
     *
     * @param AbstractClient $client
     * @param ProgressiveStoreInterface $progressiveStore
     * @param string $additionalInformationHolderClass
     */
    public function __construct(AbstractClient $client, ProgressiveStoreInterface $progressiveStore,
                                $additionalInformationHolderClass = self::MODEL_ADDITIONAL_INFORMATION_HOLDER_CLASS)
    {
        $this->client = $client;
        $this->progressiveStore = $progressiveStore;
        $this->additionalInformationHolderClass = $additionalInformationHolderClass;
    }

    /**
     * Get the existing note message
     *
     * @param \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application
     * @return string
     */
    public function getNoteMessage(ReferencingApplication $application)
    {
        $noteHolder = $this->getAdditionalInformationHolderFromProgressiveStore();

        if ($noteHolder && $noteHolder->getNoteId()) {
            return $noteHolder->getMessage();
        }

        // Exception handling to deal with the absence of a get notes command in the system context
        // this needs to be added by TBL
        // todo: add getReferencingApplicationNotes to system context in IRIS SDK
        try {
            $notes = $this
                ->client
                ->getReferencingApplicationNotes(array(
                    'applicationUuId' => $application->getReferencingApplicationUuId(),
                ))
            ;
        }
        catch (\Exception $e) {
            return '';
        }

        if (isset($notes[0]) && $notes[0] instanceof Note) {
            $this->progressiveStore->addPrototype(new AdditionalInformationHolder());
            $this->getAdditionalInformationHolderFromProgressiveStore()->setNoteId($notes[0]->getNoteId());
            return $notes[0]->getNote();
        }

        return '';
    }

    /**
     * Handles the persistence of a note (persist & update)
     *
     * @param array $formStepData
     * @param \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application
     * @return void
     */
    public function handleNotePersistence(array $formStepData, ReferencingApplication $application)
    {
        $additionalInfoNote = $this->getAdditionalInformationHolderFromProgressiveStore();

        if (!($additionalInfoNote instanceof AdditionalInformationHolder)) {
            $additionalInfoNote = new AdditionalInformationHolder();
            $this->progressiveStore->addPrototype($additionalInfoNote);
        }

        $additionalInfoMessage = '';
        if (isset($formStepData['step']['additionalInfo'])) {
            $additionalInfoMessage = $formStepData['step']['additionalInfo'];
        }

        if ($additionalInfoMessage) {

            if ($additionalInfoNote->getNoteId()) {

                // If we have an existing additional info message, update
                $this
                    ->client
                    ->updateReferencingApplicationNote(array(
                        'applicationUuId' => $application->getReferencingApplicationUuId(),
                        'noteId' => $additionalInfoNote->getNoteId(),
                        'note' => $additionalInfoMessage,
                    ))
                ;

                $additionalInfoNote
                    ->setMessage($additionalInfoMessage)
                ;
            }
            else {

                // If we don't have an existing additional info message, create
                $note = $this
                    ->client
                    ->createReferencingApplicationNote(array(
                        'applicationUuId' => $application->getReferencingApplicationUuId(),
                        'note' => $additionalInfoMessage,
                    ))
                ;

                $additionalInfoNote
                    ->setNoteId($note->getNoteId())
                    ->setMessage($additionalInfoMessage)
                ;
            }
        }
    }

    /**
     * Get the additional information holder
     * from progressive store
     *
     * @return \Iris\Referencing\FormSet\Model\AdditionalInformationHolder
     */
    private function getAdditionalInformationHolderFromProgressiveStore()
    {
        return $this->progressiveStore->getPrototypeByClass($this->additionalInformationHolderClass);
    }
}