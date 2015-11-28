<?php

class Model_Core_CustomerContactPreferences extends Model_Abstract
{
    /**
     * Available contact preference methods
     * @var integer
     */
    const PRINT_METHOD = 'PRINT';
    const EMAIL_METHOD = 'EMAIL';
    const FAX_METHOD = 'FAX';
    const NONE_METHOD = 'NONE';
    
    /**
     * List of contact preferences
     * @var array
     */
    protected $_preferences = array();
    
    /**
     * Return the contact preferences
     * 
     * @return array
     */
    public function getPreferences()
    {
        return $this->_preferences;
    }
    
    /**
     * Add a preference to the preferences list
     *
     * @param string $preference Contact Preference
     * @return void
     */
    public function addPreference($preference)
    {
        $this->_preferences[] = $preference;
    }
}
