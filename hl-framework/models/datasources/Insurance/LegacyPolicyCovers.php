<?php

class Datasource_Insurance_LegacyPolicyCovers extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'policyOptions';
    protected $_primary = 'policyOptionID';

    /**
     * Retrieve the policy cover by label
     *
     * @param $label Policy cover label
     * @return Model_Insurance_LegacyPolicyCover|null Policy cover object or null
     */
    public function getPolicyCoverByLabel($label)
    {
        $select = $this->select(array('policyOptionID', 'policyOption', 'printableName'))
                        ->where('policyOption = ?', $label)
                        ->limit(1);

        $row = $this->fetchRow($select);
        $policycover = null;

        if ($row) {
            $policycover = new Model_Insurance_LegacyPolicyCover();
            $policycover->setId($row->policyOptionID);
            $policycover->setLabel($row->policyOption);
            $policycover->setName($row->printableName);
        }

        return $policycover;
    }
}
