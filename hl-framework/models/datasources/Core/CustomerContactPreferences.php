<?php

class Datasource_Core_CustomerContactPreferences extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'policy_contactprefs';
    protected $_primary = array('policynumber', 'contactpref');
    /**#@-*/
    
    /**
     * Clear all existing customer preferences for the given policy number
     *
     * @param string $policynumber Policy number
     * @void
     */
    public function clearPreferences($policynumber)
    {
        $queuedelete = $this->getAdapter()->quoteInto('policynumber = ?', $policynumber);
        $this->delete($queuedelete);
    }
    
    /**
     * Insert the contact preferences into the database
     *
     * @param string $policynumber Policy number
     * @param Model_Core_CustomerContactPreferences $customerpref Customer preferences
     */
    public function insertPreferences($policynumber, $customerpref)
    {
        $preferences = $customerpref->getPreferences();
        
        foreach ($preferences as $preference)
        {
            // Insert the preference into the database
            $templatemodel = new Datasource_Insurance_Document_InsuranceRequestMethods();
            $contactprefid = $templatemodel->getRequestMethodId($preference);
            
            $data = array
            (
                'policynumber'         => $policynumber,
                'contactpref'          => $contactprefid,
            );
            
            $this->insert($data);
        }
    }
    
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null)
    {
	// If policyNumber is empty then assume the QHLI should be replaced with PHLI.
	if(empty($policyNumber))
	{
	    $policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
	}
	
	$where = $this->getAdapter()->quoteInto('policynumber = ?', $quoteNumber);
	$updatedData = array('policynumber' => $policyNumber);
	return $this->update($updatedData, $where);
    }
}
