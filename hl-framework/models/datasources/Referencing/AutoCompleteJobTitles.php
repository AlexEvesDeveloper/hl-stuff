<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

/**
 * Model definition for the autocomplete_job_titles datasource
 *
 * @author Alex Eves <alex.eves@barbon.com>
 *
 */
class Datasource_Referencing_AutoCompleteJobTitles extends Zend_Db_Table_Multidb
{
    /**
     * @var string
     */
    protected $_multidb = 'db_referencing';

    /**
     * @var string
     */
    protected $_name = 'autocomplete_job_titles';

    /**
     * @var string
     */
    protected $_primary = 'id';
}