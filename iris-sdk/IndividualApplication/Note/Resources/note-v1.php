<?php

/**
 * Notes Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Notes',
    'description' => 'Manage notes for an individual applicant',
    'operations' => array(
        'CreateReferencingApplicationNote' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/individual/application/{applicationUuId}/note',
            'summary' => 'Add a new note to an application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\Note\Model\Note',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'note' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'emailAssesor' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
            ),
        ),
        'UpdateReferencingApplicationNote' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/individual/application/{applicationUuId}/note/{noteId}',
            'summary' => 'Update an existing note for an application',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'noteId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'note' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                )
            ),
        ),
        'GetReferencingApplicationNotes' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/{applicationUuId}/notes',
            'summary' => 'Get all application notes',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\Note\Model\Note',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);

