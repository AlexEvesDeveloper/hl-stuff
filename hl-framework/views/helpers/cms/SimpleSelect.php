<?php

class Cms_View_Helper_SimpleSelect extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating simple select HTML fragments
     *
     * @param Zend_Form_Element_Select $element
     * @param string $cssEtc optional attribute string for <select> tag
     * @param string $wrapOptionHtml optional HTML that wraps each <option>, split by an asterisk
     *
     * @return string
     */
    public function simpleSelect($element, $cssEtc = '', $wrapOptionHtml = '') {
        $wrapOptionHtmlStart = $wrapOptionHtmlEnd = '';
        if (strpos($wrapOptionHtml, '*') !== false) {
            list($wrapOptionHtmlStart, $wrapOptionHtmlEnd) = explode('*', $wrapOptionHtml);
        }
        $output = sprintf(
            "<select name=\"%s\" id=\"%s\"%s>\n",
            //               1         2   3
            $element->getName(),    // 1
            $element->getId(),      // 2
            (($cssEtc != '') ? " {$cssEtc}" : '') // 3
        );
        $options = $element->getMultiOptions();
        foreach ($options as $key => $val) {
            $output .= sprintf(
                "  %s<option value=\"%s\"%s>%s</option>%s\n",
                //  1                 2   3  4          5
                $wrapOptionHtmlStart,   // 1
                $key,                   // 2
                (((string)$key == (string)$element->getValue() && $key !== '') ? ' selected="selected"' : ''), // 3
                $val,                   // 4
                $wrapOptionHtmlEnd      // 5
            );
        }
        $output .= "</select>\n";
        return $output;
    }

}