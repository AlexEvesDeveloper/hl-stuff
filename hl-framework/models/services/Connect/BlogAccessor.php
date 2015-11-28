<?php

/**
 * Class for remotely retrieving blog entries.
 */
class Service_Connect_BlogAccessor {
	
	/**
	 * Returns the current 'main' blog entry.
	 *
	 * @return mixed
	 * The main blog entry in an associative array, or null if there
	 * is no current main blog entry.
	 */
	public function getMainBlogEntry() {
	
		$blogEntries = new Datasource_Cms_Connect_BlogEntries();
		return $blogEntries->getByStatus(1);
	}
	
	
	/**
	 * Returns the current summary blog entries.
	 *
	 * @return mixed
	 * Returns an array of summary blog entries, or null if there are
	 * no current summary blog entries.
	 */
	public function getSummaryBlogEntries() {
	
		$blogEntries = new Datasource_Cms_Connect_BlogEntries();
		return $blogEntries->getByStatus(2);
	}
	
	
	/**
	 * Returns an associative array of all blog tags in use (mapped).
	 *
	 * @return mixed
	 * An associative array of all tags mapped against at least one blog
	 * entry, or null if there are no mapped tags.
	 */
	public function getAllTagsInUse() {
		
		$blogEntryTags = new Datasource_Cms_Connect_BlogEntryTags();
		$allTags = $blogEntryTags->getAllInUse();
		
		return $allTags;
	}
	
	
	/**
	 * Returns an associative array of all blog tags.
	 *
	 * @return mixed
	 * An associative array of all tags, or null if there are no blog tags.
	 */
	public function getAllTags() {
		
		$blogEntryTags = new Datasource_Cms_Connect_BlogEntryTags();
		$allTags = $blogEntryTags->getAll();
		
		$returnArray = array();
		foreach($allTags as $currentTag) {

			$returnArray[] = array(
				'id' => $currentTag['id'],
				'tag' => $currentTag['tag']);
		}
		
		if(empty($returnArray)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $returnArray;
		}
		return $returnVal;
	}

	
	/**
	 * Returns an array of blog entries associated with the tag string passed in.
	 * 
	 * @param string $tagName
	 * The name of the tag against which this method will try to find
	 * associated blog entries.
	 * 
	 * @return mixed
	 * Returns an array of blog entries associated with the tag name
	 * passed in, or null if there are no associated blog entries.
	 */
	public function getBlogEntriesByTagName($tagName) {
		
		$blogEntryTags = new Datasource_Cms_Connect_BlogEntryTags();
		$tagId = $blogEntryTags->getID($tagName);
		
		//Now get all the blog entries associated with that tag id.
		$blogEntryTagMap = new Datasource_Cms_Connect_BlogEntryTagMap();
		$blogEntryDetails = $blogEntryTagMap->getByTagId($tagId);
		
		$returnArray = array();
		if(!empty($blogEntryDetails)) {

			$blogEntries = new Datasource_Cms_Connect_BlogEntries();
			foreach($blogEntryDetails as $currentBlogEntry) {

				$returnArray[] = $blogEntries->getByID($currentBlogEntry->blog_entry_id);
			}
		}
		
		
		//Provide a return value consistent with this function's contract.
		if(empty($returnArray)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $returnArray;
		}

		return $returnVal;
	}
	
	
	/**
	 * Returns blog entries matching the date specified.
	 *
	 * @param string $month
	 * The full month name to search for. E.g. February
	 *
	 * @param integer $year
	 * The year to search for. E.g. 2011
	 *
	 * @return mixed
	 * Returns an array of blog entry data corresponding to the date specified,
	 * or null if no matches found.
	 */
	public function getBlogEntriesByDate($month, $year) {
		
		$blogEntries = new Datasource_Cms_Connect_BlogEntries();
		return $blogEntries->getByDate($month, $year);
	}
	
	
	/**
	 * Returns an array of all blog entries.
	 * 
	 * @return mixed
	 * Returns an array of all blog entries, or null if there are no
	 * blog entries.
	 */
	public function getAllBlogEntries() {
		
		$blogEntries = new Datasource_Cms_Connect_BlogEntries();
		return $blogEntries->getAll();
	}
	
	
	/**
	 * Returns each month for which there are one or more blog entires.
	 *
	 * @return mixed
	 * Returns an array of month strings, or null if there are no blog entries.
	 */
	public function getAllBlogMonths() {
		
		$monthArray = array();
		$blogEntries = $this->getAllBlogEntries();
		foreach($blogEntries as $currentBlogEntry) {
			
			$lastUpdated = new Zend_Date($currentBlogEntry['lastUpdated'], Zend_Date::ISO_8601);
			$monthArray[] = $lastUpdated->get(Zend_Date::MONTH_NAME);
		}
		
		if(empty($monthArray)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $monthArray;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Returns each year for which there are one or more blog entires.
	 *
	 * @return mixed
	 * Returns an array of year strings, or null if there are no blog entries.
	 */
	public function getAllBlogYears() {
		
		$yearArray = array();
		$blogEntries = $this->getAllBlogEntries();
		foreach($blogEntries as $currentBlogEntry) {
			
			$lastUpdated = new Zend_Date($currentBlogEntry['lastUpdated'], Zend_Date::ISO_8601);
			$yearArray[] = $lastUpdated->get(Zend_Date::YEAR);
		}
		
		if(empty($yearArray)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $yearArray;
		}
		
		return $returnVal;
	}
    
    
    /**
     * Returns each year/month for which there are one or more blog entires.
     *
     * @return mixed
     * Returns an array of "year month" strings, or null if there are no blog entries.
     */
    public function getAllBlogYearsMonths() {
        
        $yearMonthArray = array();
        $blogEntries = $this->getAllBlogEntries();
        foreach($blogEntries as $currentBlogEntry) {
            
            $lastUpdated = new Zend_Date($currentBlogEntry['lastUpdated'], Zend_Date::ISO_8601);
            $yearMonthArray[] = $lastUpdated->get(Zend_Date::YEAR) . ' ' . $lastUpdated->get(Zend_Date::MONTH_NAME);
        }
        
        if(empty($yearMonthArray)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $yearMonthArray;
        }
        
        return $returnVal;
    }
}

?>