<?php
/**
* Model definition for the keyhouse rent guarantee claims table.
*/
class Datasource_Insurance_KeyHouse_Claim extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'online_claims';
    protected $_primary = 'reference_number';
    /**
     * Data not to be sent to KH
     * @var array
     */
    protected $removals = array('tenancy_address_id', 'landlord_address_id');

    /**
     * Inserts a new claim.
     *
     * @param array
     *
     * @return boolean
     */
    public function save($data) {
        if(is_array($data)) {
            if($data['reference_number'] != "" && $data['agentid'] != "" &&
                $data['agentschemenumber'] != "") {
                    // Remove non-required fields
                    foreach ($this->removals as $removal) {
                        unset($data[$removal]);
                    }
                return $this->insert($data);
            }
        }
    }

    /**
     * Runs procedure to generate next number
     *
     * @param void
     * @return int nxt number
     */
    public function getNextNumber()
    {
        $nextNoProc = $this->getAdapter()->prepare(
            "DECLARE @RESULT int
             EXEC udf_gen_next_number
             @RESULT = @RESULT OUTPUT"
        );
        $nextNoProc->execute();
        // Is this really necessary? Yes, unfortunately.
        $dereference = $nextNoProc->fetch();
        return array_pop($dereference);
    }

    /**
     * Executes cleanup procedure
     *
     * @param int $referenceNo
     * @return void
     */
    public function cleanUp($referenceNo) {
        $cleanUpProc = $this->getAdapter()->prepare(
            "EXEC udf_cleanup_claim @reference_number = $referenceNo"
        );
        $cleanUpProc->execute();
    }

    /**
     *
     * Get Claim Summary for the given Agent Scheme Number
     * @param int $agentSchemeNumber
     * @return Array
     */
    public function getOpenClaims($agentSchemeNo) {
        $select = $this->getAdapter()->prepare(
        "SELECT afw.AgentSchemeNumber,
               afw.OtherRef,
               AFW.ClaimNo,
               afw.RefPolNumber,
               afw.DateStarted,
               PO.LoggedBy,
               PO.LoggedByEmail,
               afw.PROPERTYADDRESS,
               AFW.ClaimsHandler,
               AFW.ClaimsHandlerEmail,
               afw.Number,
               afw.LastActionDate,
               afw.LastActivity,
               afw.OpenOrClosed
        FROM   udf_openclaimsForWeb afw
               INNER JOIN udf_pendingOpeners PO
                 ON AFW.ClaimNo = PO.ClaimNo
        WHERE  afw.AgentSchemeNumber = '$agentSchemeNo'");
        $select->execute();
        return $select->fetchAll();
    }

    /**
     * Fetch Claim details for the given claim reference number
     *
     * @param mixed $claimRefNo
     * @param mixed $agentSchemeNo
     * @return Array
     */
    public function getClaim($claimRefNo, $agentSchemeNo) {
        $select = $this->getAdapter()->prepare(
            "SELECT
                   AFW.AgentSchemeNumber,
                   AFW.ClaimNo,
                   AFW.Status,
                   AFW.ClaimAddress,
                   AFW.ClaimDate,
                   AFW.ClaimsHandler,
                   AFW.ClaimsHandlerEmail,
                   AFW.Activity,
                   AFW.OpenOrClosed,
                   PO.LoggedBy
            FROM   dbo.udf_activityForWeb AFW
                   INNER JOIN udf_pendingOpeners PO
                     ON AFW.ClaimNo = PO.ClaimNo
            WHERE  AFW.ClaimNo = '$claimRefNo'
                   AND AFW.AgentSchemeNumber = '$agentSchemeNo'
            GROUP  BY AFW.AgentSchemeNumber,
                      AFW.ClaimNo,
                      AFW.Status,
                      AFW.ClaimAddress,
                      AFW.ClaimDate,
                      AFW.ClaimsHandler,
                      AFW.ClaimsHandlerEmail,
                      AFW.Activity,
                      AFW.OpenOrClosed,
                      PO.LoggedBy");
        $select->execute();
        $claimDetails = $select->fetchAll();

        $returnVal = array();
        if(count($claimDetails) > 0) {
            $returnVal = $claimDetails;
            foreach($returnVal as &$claim) {
                $claim['ClaimDate'] = date('d/m/Y', strtotime($claim['ClaimDate']));
            }
        }
        return $returnVal;
    }
}
?>