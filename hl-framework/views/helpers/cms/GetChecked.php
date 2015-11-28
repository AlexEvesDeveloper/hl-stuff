<?php

/**
 * Helper class for selecting checkboxes and radiobuttons. Identifies if a
 * substring exists within a string, and if yes returns 'checked="checked"'.
 */
class Cms_View_Helper_GetChecked extends Zend_View_Helper_Abstract {
	
	/**
	 * Identifies whether $needle exists in $hayStack, in which
	 * case it will return string 'checked="checked"'. Otherwise
	 * returns an empty string.
	 *
	 * @param string $hayStack
	 * The string to be analysed.
	 *
	 * @param string $needle
	 * The substring to search for in the $hayStack.
	 *
	 * @return string
	 * Returns 'checked="checked"' if the $needle is found in the $hayStack,
	 * otherwise returns empty string.
	 */
	public function getChecked($hayStack, $needle) {
		
		$returnVal = '';
		
        $hayStackArray = explode(',', $hayStack);
        foreach($hayStackArray as $currentStackItem) {
			
			if($currentStackItem == $needle) {
				
				$returnVal = 'checked="checked"';
				break;
			}
        }
		
		return $returnVal;
	}
}

?>