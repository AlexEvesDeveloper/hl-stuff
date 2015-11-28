<?php

/**
 * Primary referencing manager class providing top-level referencing services.
 * 
 * @todo
 * Migrate to using reference managers for read/write operations, rather than using
 * the datasources directly:
 * 
 * $_residentialRefereeDatasource;
 * $_residentialReferenceDatasource;
 * $_occupationDatasource;
 * $_occupationalRefereeDatasource;
 * $_occupationalReferenceDatasource;
 */
class Manager_Referencing_Reference
{
	protected $_referenceDatasource;
		
	protected $_decisionManager;
	protected $_statusManager;
	protected $_productSelectionManager;
	protected $_propertyLeaseManager;
	protected $_prospectiveLandlordManager;
	protected $_propertyAspectManager;
	protected $_customerMapManager;
	protected $_referenceSubjectManager;
	protected $_bankAccountManager;
	protected $_progressManager;
	protected $_residenceManager;
	
	protected $_residentialRefereeDatasource;
	protected $_residentialReferenceDatasource;
	
	protected $_occupationDatasource;
	protected $_occupationalRefereeDatasource;
	protected $_occupationalReferenceDatasource;
    
    public function __construct()
    {
        $this->_referenceDatasource = new Datasource_Referencing_Reference();
        
    	$this->_decisionManager = new Manager_Referencing_Decision();
        $this->_statusManager = new Manager_Referencing_Status();
        $this->_productSelectionManager = new Manager_Referencing_ProductSelection();
        $this->_propertyLeaseManager = new Manager_Referencing_PropertyLease();
        $this->_prospectiveLandlordManager = new Manager_Referencing_ProspectiveLandlord();
        $this->_propertyAspectManager = new Manager_Referencing_PropertyAspect();
        $this->_customerMapManager = new Manager_Referencing_CustomerMap();
        $this->_referenceSubjectManager = new Manager_Referencing_ReferenceSubject();
        $this->_bankAccountManager = new Manager_Referencing_BankAccount();
        $this->_progressManager = new Manager_Referencing_Progress();
        $this->_residenceManager = new Manager_Referencing_Residence();
		
		$this->_residentialRefereeDatasource = new Datasource_Referencing_ResidenceReferees();
		$this->_residentialReferenceDatasource = new Datasource_Referencing_ResidenceReferences();
		
		$this->_occupationDatasource = new Datasource_Referencing_Occupations();
		$this->_occupationalRefereeDatasource = new Datasource_Referencing_OccupationReferees();
		$this->_occupationalReferenceDatasource = new Datasource_Referencing_OccupationReferences();
    }
	
    /**
     * Inserts a new, empty Reference into the datasource and returns a corresponding object.
     *
     * This method will allocate unique internal and external Reference identifiers
     * to the new Reference.
     *
	 * @return Model_Referencing_Reference
	 * Holds the details of the newly inserted Reference.
     */
    public function createReference()
    {
		return $this->_referenceDatasource->createReference();
	}
	
	/**
	 * Updates an existing reference in the datasource.
	 *
	 * @param Model_Referencing_Reference
	 * Top level Reference object, encapsulating and linking to all the referencing details
	 * that should be updated.
	 *
	 * @return void
	 */
	public function updateReference($reference)
    {
		$this->_referenceDatasource->updateReference($reference);
		
		if (!empty($reference->customer)) {
			$this->_customerMapManager->save($reference);
		}
		
		$this->_decisionManager->save($reference->decision);
		$this->_statusManager->save($reference->status);
		$this->_productSelectionManager->save($reference->productSelection);
		$this->_propertyLeaseManager->save($reference->propertyLease);
		
		if (!empty($reference->propertyLease)) {
			$this->_propertyAspectManager->saveCollective($reference->propertyLease->propertyAspects);
			
			if (!empty($reference->propertyLease->prospectiveLandlord)) {
				$this->_prospectiveLandlordManager->save($reference->propertyLease->prospectiveLandlord);
			}
		}
		
		$this->_progressManager->save($reference->progress);

		if (!empty($reference->referenceSubject)) {
			// Update the reference subject.
			$this->_referenceSubjectManager->save($reference->referenceSubject);
			if(!empty($reference->referenceSubject->bankAccount)) {
				$this->_bankAccountManager->save($reference->referenceSubject->bankAccount);
			}
			
			// Update residences.
			if (!empty($reference->referenceSubject->residences)) {
				foreach($reference->referenceSubject->residences as $residence) {
					$this->_residenceManager->save($residence);
					
					//Update residential referees
					if (!empty($residence->refereeDetails)) {
						$this->_residentialRefereeDatasource->updateReferee($residence->refereeDetails);
						$this->_residentialReferenceDatasource->upsertReference($residence->referencingDetails);
					}
				}
			}
			
			//Update occupations.
			if (!empty($reference->referenceSubject->occupations)) {
				foreach($reference->referenceSubject->occupations as $occupation) {
					$this->_occupationDatasource->updateOccupation($occupation);
					
					//Update occupational referees and associated references
					if (!empty($occupation->refereeDetails)) {
						$this->_occupationalRefereeDatasource->updateReferee($occupation->refereeDetails);
						$this->_occupationalReferenceDatasource->upsertReference($occupation->referencingDetails);
					}
				}
			}
		}
	}
	
	/**
	 * Returns a minimal Reference object.
	 *
	 * The minimal Reference object holds attributes that are directly its own, and does
	 * NOT hold values for linked objects (such as the ReferenceSubject, PropertyLease etc).
	 * Therefore, client code would not be able, for example, so access $reference->productSelection.
	 * This method is useful for quickly accessing the internal and external reference numbers.
	 *
	 * @param mixed $referenceId
     * The unique Reference identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return mixed
     * The minimal Reference details, encapsulated in a Model_Referencing_Reference object,
     * or null if the Reference cannot be found.
	 */
	public function getMinimalReference($referenceId)
    {
		return $this->_referenceDatasource->getReferenceObject($referenceId);
	}
	
    /**
     * Retrieves the specified reference.
     *
     * @param mixed $referenceId
     * The internal or external reference identifier.
     *
     * @return mixed
     * The Reference details, encapsulated in a Model_Referencing_Reference object,
     * or null if the Reference cannot be found.
     */
    public function getReference($referenceId)
    {
    	//Get the top level Reference object.
		$reference = $this->_referenceDatasource->getReferenceObject($referenceId);
		
		$reference->decision = $this->_decisionManager->retrieve($reference->internalId);
		$reference->status = $this->_statusManager->retrieve($reference->internalId);
		$reference->productSelection = $this->_productSelectionManager->retrieve($reference->internalId);
		$reference->propertyLease = $this->_propertyLeaseManager->retrieve($reference->internalId);
		
		if (!empty($reference->propertyLease)) {
			//Get the objects linked to property lease.
			$reference->propertyLease->prospectiveLandlord = $this->_prospectiveLandlordManager->retrieve($reference->internalId);
			$reference->propertyLease->propertyAspects = $this->_propertyAspectManager->retrieve($reference->internalId);
		}
		
		$reference->customer = $this->_customerMapManager->retrieve($reference->internalId);
		$reference->progress = $this->_progressManager->retrieve($reference->internalId);
		$reference->referenceSubject = $this->_referenceSubjectManager->retrieve($reference->internalId);
		
		if (!empty($reference->referenceSubject)) {
			//Get the objects linked to the reference subject			
			$reference->referenceSubject->bankAccount = $this->_bankAccountManager->retrieve($reference->internalId);
			$reference->referenceSubject->residences = $this->_residenceManager->retrieve($reference->internalId);
			if(!empty($reference->referenceSubject->residences)) {
				
				foreach($reference->referenceSubject->residences as $residence) {
					
					$residence->refereeDetails = $this->_residentialRefereeDatasource->getByResidenceId($residence->id);
					$residence->referencingDetails = $this->_residentialReferenceDatasource->getByResidenceId($residence->id);
				}
			}
			
			$reference->referenceSubject->occupations = $this->_occupationDatasource->getByReferenceId($reference->internalId);
			if (!empty($reference->referenceSubject->occupations)) {
				foreach($reference->referenceSubject->occupations as $occupation) {
					$occupation->refereeDetails = $this->_occupationalRefereeDatasource->getByOccupationId($occupation->id);
					$occupation->referencingDetails = $this->_occupationalReferenceDatasource->getByOccupationId($occupation->id);
				}
			}
		}

		return $reference;
	}

    /**
     * Get all references for a customer id
     */
    public function getAllReferenceIds($customerId)
    {
        return $this->_referenceDatasource->getAllReferenceIds($customerId);
    }
}
