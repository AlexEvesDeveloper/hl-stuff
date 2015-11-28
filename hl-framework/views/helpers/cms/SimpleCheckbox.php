<?php

class Cms_View_Helper_SimpleCheckbox extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating simple checkbox HTML fragments
     *
     * @param Zend_Form_Element_Checkbox $element
     * @param string $cssEtc optional attribute string for <input> tags
     * @param string $wrapInputHtml optional HTML that wraps each <input>, split by an asterisk
     * @param string $wrapLabelHtml optional HTML that wraps each <label>, split by an asterisk
     *
     * @return string
     */
    public function simpleCheckbox($element, $cssEtc = '', $wrapInputHtml = '', $wrapLabelHtml = '') {
        $wrapInputHtmlStart = $wrapInputHtmlEnd = $wrapLabelHtmlStart = $wrapLabelHtmlEnd = '';
        if (strpos($wrapInputHtml, '*') !== false) {
            list($wrapInputHtmlStart, $wrapInputHtmlEnd) = explode('*', $wrapInputHtml);
        }
        if (strpos($wrapLabelHtml, '*') !== false) {
            list($wrapLabelHtmlStart, $wrapLabelHtmlEnd) = explode('*', $wrapLabelHtml);
        }
        $output = '';
        $checkedValue = $element->options['checkedValue'];
        $output .= sprintf(
            "%s<input type=\"checkbox\" name=\"%s\" id=\"%s\" value=\"%s\"%s%s />%s<label for=\"%s\">%s</label>%s%s\n",
            //1                                 2         3            4   5 6    7              8    9         A B
            $wrapInputHtmlStart,    // 1
            $element->getName(),    // 2
            $element->getId(),      // 3
            $checkedValue,          // 4
            (((string)$checkedValue == (string)$element->getValue()) ? ' checked="checked"' : ''), // 5
            (($cssEtc != '') ? " {$cssEtc}" : ''), // 6
            $wrapLabelHtmlStart,    // 7
            $element->getId(),      // 8
            $element->getLabel(),   // 9
            $wrapLabelHtmlEnd,      // A
            $wrapInputHtmlEnd       // B
        );
        return $output;
    }

}