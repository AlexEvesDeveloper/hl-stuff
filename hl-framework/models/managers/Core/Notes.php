<?php

/**
 * Manager class responsible for policy notes logic
 */
class Manager_Core_Notes {

    /**
    * Public function to save a Policy Note.
    *
    * @param string $policyNumber Policy number of the policy a note will be added to
    * @param string $dateOfEntry Date the policy note was added
    * @param string $note The note itself
    *
    * @return bool true on success, false on failure
    */
    public function save($policyNumber, $note, $dateOfEntry = null){
        if (!$dateOfEntry){
            $dateOfEntry = date("Y-m-d h:j:s"); // set to today id null
        }
        $policyNoteObject = new Model_Core_Note();
        $policyNoteObject->dateOfEntry = $dateOfEntry;
        $policyNoteObject->notes = $note;
        $policyNoteObject->policyNumber = $policyNumber;

        // Connect to the data source and do the save
        $policyNoteDataSource = new Datasource_Core_Notes();
        $policyNoteDataSource->appendToPolicyNotes($policyNoteObject);
        
    }
}

?>