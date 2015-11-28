<?php

/**
 * Model definition for the legacy policy search datasource.  Generally used by
 * the Insurance Munt Manager.
 */
class Datasource_Insurance_Legacy_DocumentQueue extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'docQueue';
    protected $_primary = 'queuenumber';
    /**#@-*/
    
    /**
     * Add a generated document to the document queue
     *
     * @param string $policynumber Policy number of document
     * @param int $csuid Csu ID
     * @param string $doc Document source
     * @param string $target Target/recipient of document - agent or holder
     * @param string $filename Filename of document
     * @return void
     */
    public function addDocument($policynumber, $csuid, $doc, $letteridlist, $email, $target, $filename)
    {
        $data = array
        (
            'policynumber'  => $policynumber,
            'csuid'         => $csuid,
            'doc'           => $doc,
            'letteridlist'  => $letteridlist,
            'emailto'       => $email,
            'target'        => $target,
            'filename'      => $filename,
        );
        
        $this->insert($data);
    }
}
