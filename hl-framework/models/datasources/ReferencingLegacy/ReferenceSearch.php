<?php

/**
* Model definition for the ReferenceSearch datasource.
*/
class Datasource_ReferencingLegacy_ReferenceSearch extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'Enquiry';
    protected $_primary = 'RefNo';
    /**#@-*/


    /**
     * Search references in the legacy datasource.
     *
     * @param mixed $agentschemeno
     * The agent scheme number.
     *
     * @param array $criteria
     * The array of search criteria.
     *
     * @param string $orderBy
     * Must correspond to one of the consts exposed by the Model_Referencing_SearchResult class.
     *
     * @param integer $pageNumber
     * The current search result page number.
     *
     * @param integer $rowLimit
     * The number of results to display on each page.
     *
     * @return Model_Referencing_SearchResult
     * Encapsulates the search results.
     */
    public function searchReferences($agentschemeno, $criteria, $orderBy, $pageNumber, $rowLimit, $offset = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name);
        $select->joinLeft('Tenant', 'Tenant.id = Enquiry.TenantID');
        $select->joinLeft('progress', 'progress.refno = Enquiry.RefNo');
        $select->joinLeft('Product', 'Product.ID = Enquiry.ProductID');
        $select->joinLeft('property', 'property.refno = Enquiry.proprefno');
        
        //Mandatory search items.
        $select->where('AgentID = ? ', $agentschemeno);
        $select->where('nochange != ? ', 'cancelled');
        $select->where('proprefno != ? ', '');
        // Don't look up any duplicates that may have been pushed in by Finance or MI events
        $select->where('Enquiry.RefNo NOT LIKE ?', 'HLT%');
        // Added during dev build and testing to remove orphan records todo: Remove for non-dev:
        //$select->where('progress.start_time IS NOT NULL', '');
        
        if(empty($criteria['refno'])) {
            //Optional search items.
            if(!empty($criteria['proprefno'])) {
                $where = $this->quoteInto('Enquiry.proprefno = ? ', $criteria['proprefno']);
                $select->where($where);
            }
            
            if (!empty($criteria['firstname']) || !empty($criteria['lastname'])) {
                // Use a sub select for the join to the tenants table - approximately 1000% quicker
                if (!empty($criteria['firstname']) && !empty($criteria['lastname'])) {
                    $where = $this->quoteInto('firstname LIKE ? AND lastname LIKE ?', $criteria['firstname'] . '%', $criteria['lastname'] . '%');
                } elseif (!empty($criteria['firstname'])) {
                    $where = $this->quoteInto('firstname LIKE ? ', $criteria['firstname'] . '%');
                } else {
                    $where = $this->quoteInto('lastname LIKE ?', $criteria['lastname'] . '%');
                }
                
                $select->where($where);
            }
            
            if(!empty($criteria['address'])) {
                $like = '%' . $criteria['address'] . '%';
                $where = $this->quoteInto('property.address1 LIKE ? ', $like);
                $select->where($where);
            }
            
            if(!empty($criteria['town'])) {
                $like = '%' . $criteria['town'] . '%';
                $where = $this->quoteInto('property.town LIKE ? ', $like);
                $select->where($where);
            }
            
            if(!empty($criteria['postcode'])) {
                $like = '%' . $criteria['postcode'] . '%';
                $where = $this->quoteInto('property.postcode LIKE ? ', $like);
                $select->where($where);
            }
            
            if(!empty($criteria['state']) && preg_match("/complete|incomplete/i", $criteria['state'])) {
                $select->where('progress.resulttx = ? ', $criteria['state']);
            }
            
            if(!empty($criteria['type'])) {
                //Add product type(s) to the query.
                $expandedSearchTerms = $this->_expandProductTypes($criteria['type']);
                if(count($expandedSearchTerms) == 1) {
                    
                    //The search term has not been expanded, so use it 'as-is'.
                    $where = $this->quoteInto('Product.Name = ? ', $criteria['type']);
                }
                else {
                    
                    //The search term is now two or more terms, so accommodate this.
                    $placeHolderString = '';
                    for($i = 0; $i < count($expandedSearchTerms); $i++) {
                        
                        if(empty($placeHolderString)) {
                            
                            $placeHolderString = '?';
                        }
                        else {
                            
                            $placeHolderString .= ',?';
                        }
                    }
                    $where = $this->quoteInto("Product.Name IN ($placeHolderString) ", $expandedSearchTerms);
                }
                $select->where($where);
            }

            
            //Order by.
            if(empty($orderBy)) {
                
                //Apply the default orderby clause.
                $select->order(array('progress.start_time DESC', 'Tenant.lastname ASC'));
            } else {
                //Apply the user-selected orderby clause.
                switch($orderBy) {
                    case Model_Referencing_SearchResult::REFNO_ASC: $select->order('Enquiry.Refno ASC'); break;
                    case Model_Referencing_SearchResult::REFNO_DESC: $select->order('Enquiry.Refno DESC'); break;
                    case Model_Referencing_SearchResult::FIRSTNAME_ASC: $select->order('Tenant.firstname ASC'); break;
                    case Model_Referencing_SearchResult::FIRSTNAME_DESC: $select->order('Tenant.firstname DESC'); break;
                    case Model_Referencing_SearchResult::LASTNAME_ASC: $select->order('Tenant.lastname ASC'); break;
                    case Model_Referencing_SearchResult::LASTNAME_DESC: $select->order('Tenant.lastname DESC'); break;
                    case Model_Referencing_SearchResult::ADDRESS1_ASC: $select->order('property.address1 ASC'); break;
                    case Model_Referencing_SearchResult::ADDRESS1_DESC: $select->order('property.address1 DESC'); break;
                    case Model_Referencing_SearchResult::STARTDATE_ASC: $select->order('progress.start_time ASC'); break;
                    case Model_Referencing_SearchResult::STARTDATE_DESC: $select->order('progress.start_time DESC'); break;
                }
            }
        }
        else {
            
            //Prepare the query based on the reference identifier only.
            $eds = new Datasource_ReferencingLegacy_Enquiry();
            switch($eds->getIdentifierType($criteria['refno'])) {
                
                case Model_Referencing_ReferenceKeyTypes::EXTERNAL:
                    $select->where('Enquiry.RefNo = ? ', (string)$criteria['refno']);
                    break;
                
                case Model_Referencing_ReferenceKeyTypes::INTERNAL:
                    $select->where('Enquiry.ID = ? ', $criteria['refno']);
                    break;
            }
        }

        // If offset supplied, add to SQL
        if (null !== $offset) {

            // If the offset is zero and row limit is zero, present only count
            if (0 == $offset && 0 == $rowLimit) {

                $select->reset(Zend_Db_Select::COLUMNS);

                $select->columns('COUNT(`Enquiry`.`ID`) AS enquiryCount');

                $select->limit($rowLimit, $offset);

                $rowSet = $this->fetchAll($select);

                //Encapsulate the results and return.
                $searchResult = new Model_Referencing_SearchResult();
                $searchResult->results = array();
                $searchResult->totalNumberOfResults = $rowSet[0]['enquiryCount'];

                return $searchResult;
            }
            else {

                $select->limit($rowLimit, $offset);

                $rowSet = $this->fetchAll($select);

                //Encapsulate the results and return.
                $searchResult = new Model_Referencing_SearchResult();
                $searchResult->results = $rowSet->toArray();

                return $searchResult;
            }
        }

        //Grouping
        $select->group('Enquiry.RefNo');

        //Run the query.
        $rowSet = $this->fetchAll($select);
        $totalNoOfResults  = $rowSet->count();

        //Apply resultset limits.
        switch($rowLimit) {
            
            case Model_Referencing_SearchResult::TEN_ROWS:
            case Model_Referencing_SearchResult::TWENTY_FIVE_ROWS:
            case Model_Referencing_SearchResult::FIFTY_ROWS:
            case Model_Referencing_SearchResult::ONE_HUNDRED_ROWS:
            case Model_Referencing_SearchResult::ALL_ROWS:
                if($rowLimit == Model_Referencing_SearchResult::ALL_ROWS) {
                    
                    $rowLimit = Model_Referencing_SearchResult::ONE_HUNDRED_ROWS;
                }
                
                //Calculate the offset.
                if($pageNumber == 1) {
                    
                    $offset = 0;
                }
                else {
                    
                    $offset = $pageNumber - 1;
                    $offset *= $rowLimit;
                }
                break;
            
            default:
                throw new Zend_Exception('Invalid row limit specified.');
        }
        
        //Prepare the resultset.
        $results = array();
        $loopCounter = 0;
        for($i = 0; $i < $totalNoOfResults; $i++) {
            
            if($i < $offset) {
                
                continue;
            }
            
            $currentRow = $rowSet->getRow($i);
            $results[$loopCounter] = $currentRow->toArray();
            
            //The following are used on the reference summary page
            $results[$loopCounter]['applicantType'] = $this->_getApplicantTypeString($currentRow);
            $results[$loopCounter]['refStatus'] = $this->_getReferenceStatusString($currentRow);
            
            $loopCounter++;
            if($loopCounter == $rowLimit) {
                
                break;
            }
        }
        
        //Prepare the SearchResult object.
        if($pageNumber > 1) {
            
            $previousPageNumber = $pageNumber - 1;
        }
        else {
            
            $previousPageNumber = null;
        }
        
        if($totalNoOfResults > ($pageNumber * $rowLimit)) {
            
            $nextPageNumber = $pageNumber + 1;
        }
        else {
            
            $nextPageNumber = null;
        }
        
        //Encapsulate the results and return.
        $searchResult = new Model_Referencing_SearchResult();
        $searchResult->results = $results;
        $searchResult->resultsPerPage = $rowLimit;
        $searchResult->previousPageNumber = $previousPageNumber;
        $searchResult->currentPageNumber = $pageNumber;
        $searchResult->nextPageNumber = $nextPageNumber;
        $searchResult->maxPageNumber = ceil($totalNoOfResults / $rowLimit);
        $searchResult->totalNumberOfResults = $totalNoOfResults;
        return $searchResult;
    }
    
    protected function _expandProductTypes($productName)
    {
        $returnArray = array();
        $returnArray[] = $productName;
        
        if(preg_match("/insight/i", $productName)) {
            $returnArray[] = 'Credit Profile Plus';
        }
        else if(preg_match("/credit profile plus/i", $productName)) {
            $returnArray[] = 'Insight';
        }
        else if(preg_match("/enhance/i", $productName)) {
            $returnArray[] = 'Comprehensive Plus';
        }
        else if(preg_match("/comprehensive plus/i", $productName)) {
            $returnArray[] = 'Enhance';
        }
        
        return $returnArray;
    }
    
    /**
     * Takes a reference row as found by searchReferences() and returns a single
     * friendly string indicating its status.
     *
     * @param array $reference
     *
     * @return string
     */
    protected function _getReferenceStatusString($reference)
    {
        $returnVal = 'Incomplete';

        if (preg_match('/^Incomplete .*/', $reference['conclusion']) == 1) {
            $returnVal = $reference['conclusion'];
        }
        else
        {
            if ($reference['result'] == 'Complete') $returnVal = 'Complete';
            if ($reference['employerref'] == 'Incomplete') $returnVal = 'Waiting for Employer ref';
            if ($reference['landlordref'] == 'Incomplete') $returnVal = 'Waiting for Landlord ref';
            if ($reference['creditreference'] == 'No') $returnVal = 'Waiting for Credit score';
            if ($reference['txtobureau'] == 'No') $returnVal = 'Waiting for Credit score';
            if ($reference['termsagreed'] == 'No') $returnVal = 'Waiting for term agreement';
            if ($reference['paidfor'] == 'No') $returnVal = 'Waiting for payment';
            if ($reference['llapproved'] == 'No') $returnVal = 'Waiting for Landlord approval';
            if ($reference['employdetails'] == 'Incomplete') $returnVal = 'Incomplete employment details';
            if ($reference['plandlorddetails'] == 'Incomplete') $returnVal = 'Incomplete landlord details';
            if ($reference['tenantdetails'] == 'Incomplete') $returnVal = 'Incomplete tenant details';
            if ($reference['tenantaddressdetails'] == 'Incomplete') $returnVal = 'Incomplete tenant address details';
            if ($reference['propertydetails'] == 'Incomplete') $returnVal = 'Incomplete property details';
            if ($reference['propertylandlorddetails'] == 'Incomplete') $returnVal = 'Incomplete landlord property details';
            if ($reference['bankdetails'] == 'Incomplete') $returnVal = 'Incomplete bank details';
        }

        return $returnVal;
    }
    
    /**
     * Takes a reference row as found by searchReferences() and returns a single
     * friendly string indicating its applicant type ('Tenant' or 'Guarantor').
     *
     * @param array $reference
     *
     * @return string
     */
    protected function _getApplicantTypeString($reference)
    {
        $returnVal = 'Applicant';
        
        if (isset($reference['Guarantor']) && $reference['Guarantor'] == '1') {
            $returnVal = 'Guarantor';
        }
        
        return $returnVal;
    }
}
