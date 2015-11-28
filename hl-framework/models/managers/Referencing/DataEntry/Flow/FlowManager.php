<?php

/*
 * AbstractFlow objects expose methods which can be used to control and navigate through the data entry process.
 */
abstract class Manager_Referencing_DataEntry_Flow_FlowManager {

    public $currentFlowItem;
    
    /**
     * Moves the internal pointer to the previous step in the data entry process.
     *
     * @param mixed $referenceId
     * The unique Reference identifier (internal or external). Sometimes used to
     * identify the previous step in the data entry process.
     *
     * @return boolean
     * Returns true if the pointer is moved, false otherwise.
     */
    public abstract function moveToPrevious($referenceId = null);
    
    
    /**
     * Moves the internal pointer to the next step in the data entry process.
     *
     * @param mixed $referenceId
     * The unique Reference identifier (internal or external). Sometimes used to
     * identify the next step in the data entry process.
     *
     * @return boolean
     * Returns true if the pointer is moved, false otherwise.
     */
    public abstract function moveToNext($referenceId = null);
}

?>