<?php

/**
 * Helper class for decoding HTML strings, useful for when the HTML strings
 * have been retrieved from a database.
 */
class Cms_View_Helper_HtmlEncoder extends Zend_View_Helper_Abstract
{
	/**
	 * Decodes encoded HTML string. Example use:
	 *
	 * $htmlEncoder = new HtmlEncoder();
	 * $encodedString = "&lt;div&gt;Hello world&lt;/div&gt;";
	 * $decodedString = $htmlEncoder->htmlEncoder($encodedString);
	 * //$decodedString now is "<div>Hello world</div>"
	 *
	 * @param string $content
	 * The encoded HTML string.
	 *
	 * @param boolean $removeEnclosingApostrophes
	 * If true, will force the function to remove leading and trailing apostrophes
	 * from the $content string. True by default.
	 * 
	 * @return string
	 * The decoded HTML string.
	 */
	public function htmlEncoder($content, $removeEnclosingApostrophes = true) {
		
		$content = html_entity_decode($content);
		
		//Remove encapsulating apostrophes, if required.
		if($removeEnclosingApostrophes) {
			
			$content = preg_replace("/^\'/", '', $content);
			$content = preg_replace("/\'$/", '', $content);
		}
		 
		return $content;
	}
}

?>