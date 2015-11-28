<?php

/**
* Holds groups of SortCodeAccountMultipliers. Enables the bank validation logic to apply
* groups of tests against a bankaccount and sortcode until one matches or all tests are
* complete.
*/
class Model_Core_Bank_SortCodeAccountMultiplerGroup {

	public $multipliers = array();
}
?>