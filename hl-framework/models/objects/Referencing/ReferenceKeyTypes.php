<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the Reference
 * key types. The key types are internal (IRN) and external (ERN). The IRN is comprised of
 * digits only, whilst the ERN is comprised of a period and optional forward slash.
 */
class Model_Referencing_ReferenceKeyTypes extends Model_Abstract {
	
    const INTERNAL = 1;
    const EXTERNAL = 2;
}

?>