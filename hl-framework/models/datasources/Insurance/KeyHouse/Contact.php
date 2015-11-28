<?php
/**
 * Some utilities for the Contacts table in SQL Server
 */
class Datasource_Insurance_KeyHouse_Contact extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'Contacts';
    protected $_primary = 'Code';
    private static $CONTACT_PREFIX = 'A';

    /**
     * Gets the next available Code for the Contacts table.
     *
     * @param void.
     * @return string Next available code.
     */
    public function getNextCode() {
        $nextNoPrep = $this->getAdapter()->prepare("
            SELECT CONVERT(INT, RIGHT(MAX(Code), 5))
            FROM keyhouse.dbo.Contacts
            WHERE Code LIKE 'A[^A-Z][^a-z]%'
        ");
        $nextNoPrep->execute();
        $dereference = $nextNoPrep->fetch();
        $curr = array_pop($dereference);
        return sprintf(
            self::$CONTACT_PREFIX . "%s",
            str_pad(++$curr, 5, '0', STR_PAD_LEFT)
        );
    }

    /**
     * Checks for whether an agent already exists in the Contacts table. Returns
     * True if it does, False otherwise.
     *
     * @param $asn Agent scheme number.
     * @return Boolean Is duplicate or not.
     */
    public function isDuplicate($asn) {
        $dupCheckPrep = $this->getAdapter()->prepare("
            SELECT Code FROM keyhouse.dbo.Contacts
            WHERE Convert(varchar(10), RTrim(IsNull(OtherRef, '')))
            = Convert(varchar(10), RTrim(IsNull('" . $asn . "', '')))
        ");
        $dupCheckPrep->execute();
        $dereference = $dupCheckPrep->fetch();
        if ($dereference) {
            return $dereference['Code'];
        } else {
            return False;
        }
    }

    /**
     * Overrides Zend DB Table's insert method
     *
     * @param $contact_arr A contact array
     * @return Boolean success
     */
    public function insert(array $contact_arr) {
        // Replace single quotes with two...
        function rep($a) {
            return str_replace("'", "''", $a);
        }
        $contact_arr = array_map('rep', $contact_arr);
        // Form up SQL
        $sql = sprintf("INSERT INTO keyhouse.dbo.Contacts
                (Code, Name, Address, Salut, Tel, Fax, email, StartDate,
                    OtherAddress, OtherRef, Client, Nation, BillBal, OutlayBal,
                    ClientCur, ClientDep, CurBillBal, CurOutlayBal,
                    CurClientCBal, CurClientDBal, CompBillOnOff, CompOutlayLimit,
                    CompFeesLimit, CompTotalLimit, Approved
                ) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
                    '%s', 'Y', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
                )",
                $contact_arr['Code'], $contact_arr['Name'],
                $contact_arr['Address'], $contact_arr['Salut'],
                $contact_arr['Tel'], $contact_arr['Fax'],
                $contact_arr['email'], $contact_arr['StartDate'],
                $contact_arr['OtherAddress'], $contact_arr['OtherRef']);
        $insert = $this->getAdapter()->prepare($sql);
        $insert->execute();
    }
}