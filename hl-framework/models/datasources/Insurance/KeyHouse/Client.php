<?php
/**
 * Some utilities for the Contacts table in SQL Server
 */
class Datasource_Insurance_KeyHouse_Client extends Zend_Db_Table_Multidb
{

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'client';
    protected $_primary = 'CLCODE';

    /**
     * Checks for whether an agent already exists in the Client table. Returns
     * True if it does, False otherwise.
     *
     * @param $clientCode Keyhouse client code.
     * @return Boolean Is duplicate or not.
     */
    public function isDuplicate($clientCode) {
        $sql =
        $dupCheckPrep = $this->getAdapter()->prepare("
            SELECT CLCODE FROM keyhouse.dbo.client
            WHERE Convert(varchar(10), RTrim(IsNull(CLCODE, '')))
            = Convert(varchar(10), RTrim(IsNull('" . $clientCode . "', '')))
        ");
        $dupCheckPrep->execute();
        $dereference = $dupCheckPrep->fetch();
        if ($dereference) {
            return True;
        } else {
            return False;
        }
    }

    /**
     * Overrides Zend DB Table's insert method
     *
     * @param $contact_obj A contact array
     * @return Boolean success
     */
    public function insert(array $contact_arr) {
        // Replace single quotes with two...
        function reptwo($a) {
            return str_replace("'", "''", $a);
        }
        $contact_arr = array_map('reptwo', $contact_arr);
        // Form up SQL
        $sql = sprintf("INSERT INTO keyhouse.dbo.client
                (CLCODE, CLNAME, CLFNR, CLADDR, CLLEGALNAME, CLLEGALADDR, CLTEL,
                 CLFAX, CLEMAIL, CLNEXTCSENO, CLCSECOUNT, CLNUMCHILD,
                 DCOUMENTFOLDER
                ) VALUES (
                    '%s', '%s', '', '%s', '%s', '%s', '%s', '%s', '%s', 0,
                    0, 0, '%s'
                )",
                $contact_arr['Code'], $contact_arr['Name'],
                $contact_arr['Address'], $contact_arr['Name'],
                $contact_arr['Address'], $contact_arr['Tel'],
                $contact_arr['Fax'], $contact_arr['email'],
                $contact_arr['doc_folder']);
        $insert = $this->getAdapter()->prepare($sql);
        $insert->execute();
    }
}