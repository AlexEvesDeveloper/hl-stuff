<?php
/**
 * Some utilities for the Contacts table in SQL Server
 */
class Datasource_Insurance_KeyHouse_ClientContacts extends Zend_Db_Table_Multidb
{

    protected $_multidb = 'db_keyhouse';

    /**
     * Checks for whether an agent already exists in the ClientContacts table.
     * Returns True if it does, False otherwise.
     *
     * @param $clientCode Keyhouse client code.
     * @return Boolean Is duplicate or not.
     */
    public function isDuplicate($clientCode) {
        $sql =
        $dupCheckPrep = $this->getAdapter()->prepare("
            SELECT CLCODE FROM keyhouse.dbo.ClientContacts
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
        function repthree($a) {
            return str_replace("'", "''", $a);
        }
        $contact_arr = array_map('repthree', $contact_arr);
        // Create first and lastnames if possible
        $split = explode(' ', $contact_arr['Name']);
        isset($split[0]) ? $firstname = $split[0] : $firstname = $contact_arr['Name'];
        isset($split[1]) ? $lastname = $split[1] : $lastname = '';
        // Go ahead and insert
        $sql = sprintf("INSERT INTO keyhouse.dbo.ClientContacts
                (
                    CLCODE, CLNUMCONTACT, CLNAMECON, CLSALUTE,
                    FIRSTNAME, SURNAME
                ) VALUES (
                    '%s', '%s', '%s', '%s', '%s', '%s'
                )",
                $contact_arr['Code'], 1, $contact_arr['Name'],
                $contact_arr['Salut'], $firstname,
                $lastname);
        $insert = $this->getAdapter()->prepare($sql);
        $insert->execute();
    }
}