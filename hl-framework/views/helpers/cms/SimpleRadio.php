<?php

class Cms_View_Helper_SimpleRadio extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating simple radio button HTML fragments
     *
     * @param Zend_Form_Element_Radio $element
     * @param string $cssEtc optional attribute string for <input> tags
     * @param string $wrapInputHtml optional HTML that wraps each <input>, split by an asterisk
     * @param string $wrapLabelHtml optional HTML that wraps each <label>, split by an asterisk
     *
     * @return string
     */
    public function simpleRadio($element, $cssEtc = '', $wrapInputHtml = '', $wrapLabelHtml = '') {
        $wrapInputHtmlStart = $wrapInputHtmlEnd = $wrapLabelHtmlStart = $wrapLabelHtmlEnd = '';
        if (strpos($wrapInputHtml, '*') !== false) {
            list($wrapInputHtmlStart, $wrapInputHtmlEnd) = explode('*', $wrapInputHtml);
        }
        if (strpos($wrapLabelHtml, '*') !== false) {
            list($wrapLabelHtmlStart, $wrapLabelHtmlEnd) = explode('*', $wrapLabelHtml);
        }
        $output = '';
        $options = $element->getMultiOptions();
        foreach ($options as $key => $val) {
            $output .= sprintf(
                "%s<input type=\"radio\" name=\"%s\" id=\"%s\" value=\"%s\" %s%s/>%s<label for=\"%s\">%s</label>%s%s\n",
                //1                              2         3            4    5 6   7              8    9         A B
                $wrapInputHtmlStart,    // 1
                $element->getName(),    // 2
                $element->getId() . '-' . $key, // 3
                $key,                   // 4
                (((string)$key == (string)$element->getValue() && $key !== '') ? ' checked="checked"' : ''), // 5
                (($cssEtc != '') ? " {$cssEtc}" : ''), // 6
                $wrapLabelHtmlStart,    // 7
                $element->getId() . '-' . $key, // 8
                $val,                   // 9
                $wrapLabelHtmlEnd,      // A
                $wrapInputHtmlEnd       // B
            );
        }
        return $output;
    }

}