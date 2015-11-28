<?php

namespace Barbondev\IRISSDK\IndividualApplication\Note;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class NotesClient
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Note
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\IndividualApplication\Note\Model\Note createReferencingApplicationNote(array $args = array())
 * @method \Guzzle\Http\Message\Response updateReferencingApplicationNote(array $args = array())
 * @method \Guzzle\Common\Collection getReferencingApplicationNotes(array $args = array())
 */
class NoteClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return NoteClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/note-v%s.php',
            ))
            ->build()
        ;
    }
}