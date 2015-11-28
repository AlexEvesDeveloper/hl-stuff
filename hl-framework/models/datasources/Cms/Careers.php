<?php
/**
* Model definition for the cms careers table
* 
*/
class Datasource_Cms_Careers extends Zend_Db_Table_Multidb {
    protected $_name = 'careers';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    /**
     * Update an existing job vacancy
     *
     * @param string title
     * @param string location
     */
    public function saveChanges($vacancyID, $jobTitle, $reportingTo, $location, $startDate, $endDate, $description) {
        $mysqlStartDate = Application_Core_Utilities::ukDateToMysql($startDate);
        $mysqlEndDate = Application_Core_Utilities::ukDateToMysql($endDate);
        
        $data = array(
            'id'            =>  $vacancyID,
            'title'         =>  $jobTitle,
            'reporting_to'  =>  $reportingTo,
            'location'      =>  $location,
            'start_date'    =>  $mysqlStartDate,
            'end_date'      =>  $mysqlEndDate,
            'description'   =>  $description
        );
        
        $where = $this->quoteInto('id = ?', $vacancyID);
        $this->update($data, $where);
    }
    
    
    
    /**
     * Add a new job vacancy
     *
     */
    public function addNew($jobTitle, $reportingTo, $location, $startDate, $endDate, $description) {
        $mysqlStartDate = Application_Core_Utilities::ukDateToMysql($startDate);
        $mysqlEndDate = Application_Core_Utilities::ukDateToMysql($endDate);
        
        $data = array(
            'title'         =>  $jobTitle,
            'reporting_to'  =>  $reportingTo,
            'location'      =>  $location,
            'start_date'    =>  $mysqlStartDate,
            'end_date'      =>  $mysqlEndDate,
            'description'   =>  $description
        );
        
        return $this->insert($data);
    }
    
    
    
    /**
     * Returns a single advert by ID
     *
     * @param int vacancyID
     * @return array
     */
    public function getByID($vacancyID) {
        $select = $this->select();
        $select->where('id = ?', $vacancyID);
        $vacancy = $this->fetchRow($select);
        return array(
            'id'            =>  $vacancy->id,
            'title'         =>  $vacancy->title,
            'location'      =>  $vacancy->location,
            'description'   =>  $vacancy->description,
            'reportingTo'   =>  $vacancy->reporting_to,
            'startDate'     =>  date('d/m/Y',strtotime($vacancy->start_date)),
            'endDate'       =>  date('d/m/Y',strtotime($vacancy->end_date))
        );
    }
    
    
    
    /**
     * Returns an array of active career adverts
     *
     * @return array
     */
    public function getActive() {
        $select = $this->select();
        $select->where('start_date <= ?', date('Y-m-d'));
        $select->where('end_date >= ?', date('Y-m-d'));
        
        $activeCareers = $this->fetchAll($select);
        
        $returnArray = array();
        foreach ($activeCareers as $career) {
            $applyUrl = '/careers/apply/' . $career->id . '-' . urlencode(str_replace(' ','-',$career->title));
            array_push($returnArray, array(
                'id'            =>  $career->id,
                'title'         =>  $career->title,
                'location'      =>  $career->location,
                'description'   =>  $career->description,
                'reportingTo'   =>  $career->reporting_to,
                'startDate'     =>  date('d/m/Y',strtotime($career->start_date)),
                'endDate'       =>  date('d/m/Y',strtotime($career->end_date)),
                'applyUrl'      =>  $applyUrl
            ));
        }
        
        return $returnArray;
    }
    
    
    
    /**
     * Returns an array of expired career adverts
     *
     * @return array
     */
    public function getExpired() {
        $select = $this->select();
        $select->where('end_date < ?', date('Y-m-d'));
        
        $expiredCareers = $this->fetchAll($select);
        
        $returnArray = array();
        foreach ($expiredCareers as $career) {
            array_push($returnArray, array(
                'id'            =>  $career->id,
                'title'         =>  $career->title,
                'location'      =>  $career->location,
                'description'   =>  $career->description,
                'reportingTo'   =>  $career->reporting_to,
                'startDate'     =>  date('d/m/Y',strtotime($career->start_date)),
                'endDate'       =>  date('d/m/Y',strtotime($career->end_date))
            ));
        }
        
        return $returnArray;
    }
    
    
    
    /**
     * Returns an array of expired career adverts
     *
     * @return array
     */
    public function getFuture() {
        $select = $this->select();
        $select->where('start_date > ?', date('Y-m-d'));
        
        $futureCareers = $this->fetchAll($select);
        
        $returnArray = array();
        foreach ($futureCareers as $career) {
            array_push($returnArray, array(
                'id'            =>  $career->id,
                'title'         =>  $career->title,
                'location'      =>  $career->location,
                'description'   =>  $career->description,
                'reportingTo'   =>  $career->reporting_to,
                'startDate'     =>  date('d/m/Y',strtotime($career->start_date)),
                'endDate'       =>  date('d/m/Y',strtotime($career->end_date))
            ));
        }
        
        return $returnArray;
    }
    
    
    
    /**
     * This function will delete an existing vacancy
     *
     * @param int articleID
     *
     */
    public function remove($vacancyID) {
        $where = $this->quoteInto('id = ?', $vacancyID);
        $this->delete($where);
    }
}
?>