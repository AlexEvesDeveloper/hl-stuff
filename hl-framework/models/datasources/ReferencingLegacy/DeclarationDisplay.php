<?php

/**
* Model definition for the declaration_display datasource.
*/
class Datasource_ReferencingLegacy_DeclarationDisplay extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'declaration_display';
    protected $_primary = 'agentschemeno';
    /**#@-*/
    
    
    /**
     * Indicates whether display declaration document to an agent.
     *
     * @param int $agentschemeno
     * @return boolean
     */
    public function canDisplayDeclaration($agentSchemeNumber) {
        
        $select = $this->select();
        $select->where("display_status = 'off' and agentschemeno = ?", $agentSchemeNumber);
        $row = $this->fetchRow($select);
        
        if ( ! empty($row)) {
            return false;
        }

        return true;
    }
}

?>
