<?php

class Cms_View_Helper_YourClaims extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating claims history summary HTML fragment
     *
     * @return string
     */
    public function yourClaims() {
        $output = '';

        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        // Invoke previous claims manager
        $claimsManager = new Manager_Insurance_PreviousClaims();

        // Fetch all claims
        $claimData = $claimsManager->getPreviousClaims($pageSession->CustomerRefNo);

        // Fetch claim descriptions
        $claimDescriptionsObj = $claimsManager->getPreviousClaimTypes(Model_Insurance_ProductNames::TENANTCONTENTSPLUS);
        
        // Transform object array into a somewhat more usefully indexed descriptions array
        $claimDescriptions = array();
        foreach($claimDescriptionsObj as $claimDescriptionObj) {
            $claimDescriptions[$claimDescriptionObj->getClaimTypeID()] = $claimDescriptionObj->getClaimTypeText();
        }

        if (count($claimData) > 0) {
            $output .= '<table class="table table-bordered table-condensed table-possessions">'; // Container with indent
            $output .= "  <tr>\n"; // Line container with bottom edge
            $output .= "    <th>Claims you have added to your policy</th>\n";
            $output .= "    <th>Value</th>\n";
            $output .= "    <th></th>\n";
            $output .= "  </tr>\n";
            foreach($claimData as $key => $claim) {
                $output .= "  <tr>\n"; // Line container with bottom edge
                $output .= "    <td class=\"description\">" . $claimDescriptions[$claim->getClaimType()->getClaimTypeID()] . '<br />Date: ' . $claim->getClaimMonth() . '/' . $claim->getClaimYear() . "</td>\n";
                $output .= "    <td class=\"price\">&pound;" . number_format($claim->getClaimValue()->getValue()) . "</td>\n";
                $output .= "    <td><a id=\"removeClaim{$key}\" href=\"#\" onclick=\"removeClaimClick({$key}); return false;\" class=\"tertiary-colour\">Remove claim</a></td>\n";
                $output .= "  </tr>\n";
            }
            $output .= '</table>';
        }

        return $output;
    }

}