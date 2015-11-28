<?php

/**
 * Helper class which truncate strings so that they fit nicely in table cells
 * onscreen.
 */
class Cms_View_Helper_Truncate extends Zend_View_Helper_Abstract
{
	/**
	 * Truncates the $string passed in, from the $start to the $length, and optionally
	 * appends a $prefix and/or $postfix, to the string to indicate it is truncated.
	 *
	 * @param string $string
	 * The string to truncate.
	 *
	 * @param int $start
	 * The starting index. 0 by default.
	 *
	 * @param int $length
	 * The length after which the string should be truncated. Default 50.
	 *
	 * @param string $prefix
	 * The prefix to be prepended to the $string. Default is '...'.
	 *
	 * @param string $postfix
	 * The postfix to be appended to the $string. Default is '...'.
	 *
	 * @return string
	 * The truncated string.
	 */
	public function truncate($string, $start = 0, $length = 50, $prefix = '...', $postfix = '...') {
		
		$truncated = trim($string);
		$start = (int) $start;
		$length = (int) $length;
					 
		// Return original string if max length is 0
		if ($length < 1) return $truncated;
		 
		$full_length = iconv_strlen($truncated);
		
		// Truncate if necessary
		if ($full_length > $length) {
		
			// Right-clipped
			if ($length + $start > $full_length) {
				
				$start = $full_length - $length;
				$postfix = '';
			}
			
			// Left-clipped
			if ($start == 0) $prefix = '';
			
			// Do truncate!
			$truncated = $prefix . trim(substr($truncated, $start, $length)) . $postfix;
		}
		 
		return $truncated;
	}
}

?>