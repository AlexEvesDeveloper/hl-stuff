<?php

/**
 * Model definition for policy datasource.
 */
class Datasource_Insurance_TenantsContentsPlus_Policies extends Datasource_Insurance_LegacyQuotes {
    
    protected $_name = 'policy';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    
}

?>