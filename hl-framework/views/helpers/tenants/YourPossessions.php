<?php

class Cms_View_Helper_YourPossessions extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating possessions summary HTML fragment
     *
     * @return string
     * @munt 10 HTML in strings? REALLY? That's what partials are for. Also in-line styles. Seriously. I'm going grey because of this!! - PB
     */
    public function yourPossessions() {
        $output = '';

        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        $possession = new Datasource_Insurance_Policy_SpecPossessions($pageSession->PolicyNumber);
        $possessionsData = $possession->listPossessions();

        if (count($possessionsData) > 0) {
            $output .= '<table class="table table-bordered table-condensed table-possessions">';
            $output .= '<thead>';
            $output .= '  <tr>';
            $output .= '    <th>Items you have added to your policy</th>';
            $output .= '    <th colspan="2">Value</th>';
            $output .= '  </tr>';
            $output .= '</thead>';
            $output .= '<tbody>';
            foreach($possessionsData as $key => $possession) {
                $output .= '  <tr>'; // Line container with bottom edge
                $output .= "    <td class='description'>{$possession['category']} - Description: {$possession['description']}</td>";
                $output .= "    <td class='price'>&pound;" . number_format(doubleval($possession['value']), 2) . "</td>";
                $output .= "    <td><a id=\"removePossession{$key}\" onclick=\"removePossessionClick('{$key}'); return false;\" class=\"tertiary-colour\" href=\"#\">Remove item</a></td>";
                $output .= "  </tr>";
            }
            $output .= '</tbody>';
            $output .= "</table>";
        }

        return $output;
    }

}
