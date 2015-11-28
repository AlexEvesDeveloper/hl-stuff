<?php
class Auth_CmsAdmin {
    
    /**
     * Returns an auth adapter for the CMS Admin login systems
     *
     * @param array params
     * @return Zend_Auth_Adapter_DbTable
     *
     * This function takes a params array (which should be login form values)
     * and creates a zend auth adapter linked to the correct database
     * and users table. If the params array has come from a login form and has
     * a username and password fields it will set them as the identity
     * and credentials in the auth adapter so that we can check to see if they
     * are valid
     */
    
    public static function getAdapter(array $params) {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db_homelet_cms'));
        $authAdapter
            ->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?)');
        
        $authAdapter->setIdentity($params['username']);
        $authAdapter->setCredential($params['password']);
        
        return $authAdapter;
    }
}
?>