<?php

class Datasource_Insurance_LandlordsPlus_RiskAreas extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'UWAutoTerms';
    protected $_primary = 'PC_COMPRESS';

    /**
     * Gets an array of risk areas by the postcode
     *
     * @param string $postcode
     * @return array
     * @throws Exception
     */
    public function getByPostcode($postcode)
    {
        // Clean up the postcode
        $postcode = strtoupper($postcode);
        $postcode = str_replace(' ', '', $postcode);

        $select = $this->select()->where('PC_COMPRESS = ?', $postcode);
        $riskAreaRow = $this->fetchRow($select);

        if (0 < count($riskAreaRow)) {
            return array(
                'buildingsAreaID'   => $riskAreaRow->B_AREA,
                'contentsAreaID'    => $riskAreaRow->C_AREA,
                'floodArea'         => $riskAreaRow->FLD_FLAG,
                'subsidenceArea'    => $riskAreaRow->SUB_FLAG
            );
        }
        else {
            throw new Datasource_Exception_PostcodeNotFoundException(
                sprintf('Postcode not found while searching for LI+ Risk Area (%s)', $postcode)
            );
        }
    }
}