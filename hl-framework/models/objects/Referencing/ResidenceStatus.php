<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * status of a reference subject's residence.
 */
class Model_Referencing_ResidenceStatus extends Model_Abstract {
	
	const OWNER = 1;
	const TENANT = 2;
	const LIVING_WITH_RELATIVES = 3;
	
	public static function toString($status) {
		
		$returnVal = null;
		switch($status) {
			
			case self::OWNER: $returnVal = 'Owner'; break;
			case self::TENANT: $returnVal = 'Tenant'; break;
			case self::LIVING_WITH_RELATIVES: $returnVal = 'Living with relatives'; break;
			default: throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': unknown status');
		}
		return $returnVal;
	}
}

?>