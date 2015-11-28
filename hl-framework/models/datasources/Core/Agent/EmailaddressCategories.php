<?php
/**
 * Model definition for the agent email category table
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_EmailaddressCategories extends Zend_Db_Table_Multidb {
    protected $_name = 'agent_email_categories';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';
    protected $_referenceMap = array(
        'Emailaddress_Categories' => array(
            'columns'       =>  array('id'),
            'refTableClass' =>  'Model_Agent_Emailaddresses',
            'refColumns'    =>  array('category_id')
    ));

    /**
     * List all the email address categories using the category description as the array key
     *
     * @return array
     */
    public function listByDescription() {
        // Get all the entries in the table
        $categories = $this->fetchAll();

        $list = array();
        foreach ($categories as $category) {
            $list[$category->category] = $category->id;
        }
        return $list;
    }


    /**
     * List all the email address categories using the category ID as the array key
     *
     * @return array
     */
    public function listByID() {
        // Get all the entries in the table
        $categories = $this->fetchAll();

        $list = array();
        foreach ($categories as $category) {
            $list[$category->id] = $category->category;
        }
        return $list;
    }


    /**
     * Try to find an ID for a specific category, returns 0 if category isn't found
     *
     * @param string category
     * @return int
     */
    public function getID($category) {
        $select = $this->select()->where('category = ?',$category);
        $category = $this->fetchAll($select);
        // No warning given as this is a common/normal scenario
        if (count($category)==0) return 0;
        return $category->current()->id;
    }


    /**
    * Finds a specific agent
    *
    * @param int agentSchemeNo
    * @return Zend_Db_Select
    *
    */
    public function getByCategoryID($categoryID) {

        $select = $this->select();
        return $this->select()->where('id = ?', $categoryID);
    }
}
?>