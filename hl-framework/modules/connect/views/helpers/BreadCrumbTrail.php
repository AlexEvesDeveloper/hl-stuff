<?php

class Connect_View_Helper_BreadCrumbTrail extends Zend_View_Helper_Abstract
{

    private $_rewrites;
    private $_exclusions;
    private $_translations;
    private $_reverseRewrites;

    public function __construct() {

        // Convert "special" URLs before breadcrumb processing, eg, for Rent
        //   Guarantee Claims
        $this->_rewrites = array(
            // Simple path-based conversions
            'path' => array(
                'rentguaranteeclaims' => 'rentguarantee/claims'
            ),
            // Regular expression-based conversions
            'regex' => array(
                'moreproducts\/(advantage|business-care|vizzihome)' => 'moreproducts/benefits/$1',
                'rentguarantee\/claims\/([^dh].*)' => 'rentguarantee/claims/$1'
            )
        );

        // Any URLs that match this list will NOT have a breadcrumb trail
        //   generated for them here - typically for pages that are dynamic and
        //   generate their own.
        $this->_exclusions = array(
            // Simple path-based exclusions
            'path' => array(
                '',
                'news'
            ),
            // Regular expression-based exclusions
            'regex' => array(
                '^news\/.*',
                '^referencing\/summary.*'
            )
        );

        // Any slugs that need expansion go here
        $this->_translations = array(
            // Simple path-based translations
            'path' => array(
                '/referencing/homelet-verify' => 'HomeLet Verify',
                '/referencing/info/applicationforms' => 'Referencing Application Forms',
                '/rentguarantee' => 'Rent Guarantee &amp; Eviction Services',
                '/rentguarantee/absolute' => 'Application',
                '/rentguarantee/claims/saveclaim' => 'Save Claim',
                '/rentguarantee/claims/callmesent' => 'Email Sent',
                '/rentguarantee/products' => 'Your Rent Guarantee Products',
                '/insurance/quote' => 'Generate a Quote',
                '/insurance/products' => 'Product Information',
                '/insurance/info/keyfacts' => 'Key Facts',
                '/insurance/info/policywordings' => 'Policy Wordings',
                '/moreproducts' => 'More Products &amp; Services',
                '/moreproducts/pi' => 'Property Agent Professional Insurance',
                '/moreproducts/newcustomerdocs' => 'New Customer Documents',
                '/moreproducts/newcustomerdocs/welcomepack' => 'Useful Documents',
                '/moreproducts/newcustomerdocs/referencingdocs' => 'Referencing',
                '/moreproducts/newcustomerdocs/referencingdocs/applicationforms' => 'Application Forms',
                '/moreproducts/newcustomerdocs/rentguarantee' => 'Rent Guarantee',
                '/moreproducts/complianceinfo' => 'Compliance Information',
                '/moreproducts/complianceinfo/ds' => 'Data Security',                
                '/moreproducts/complianceinfo/tcf' => 'Treating Customers Fairly',
                '/rentguarantee/rent-recovery-plus' => 'RRPI Application',
            ),
            // Regular expression-based translations
            'regex' => array(
                '\/info$' => 'Useful Information and Documents',
                '^\/referencing\/search.*' => 'Search Results',
                '^\/insurance\/search-policy.*' => 'Policy Search Results',
                '^\/insurance\/search-customer.*' => 'Customer Search Results'
            )
        );

        $this->_reverseRewrites = array(
            'path' => array(
                '/rentguarantee/claims' => '/rentguaranteeclaims/home',
                '/rentguarantee/claims/home' => '/rentguaranteeclaims/home'
            )
        );
    }

    public function breadCrumbTrail() {
        $requesturi = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

        // Remove trailing query string data
        $requesturi = preg_replace('/\?.*$/', '', $requesturi);
        $thisUrl = trim($requesturi, '/');

        // URL rewrites - simple path
        if (isset($this->_rewrites['path'])) {
            $thisUrl = str_replace(array_keys($this->_rewrites['path']), $this->_rewrites['path'], $thisUrl);
        }

        // URL rewrites - regex
        if (isset($this->_rewrites['regex'])) {
            foreach($this->_rewrites['regex'] as $from => $to) {
                $thisUrl = preg_replace("/{$from}/", $to, $thisUrl);
            }
        }

        // Check that URL isn't to be excluded
        if (!$this->_exclude($thisUrl)) {

            // Build breadcrumb array
            $slugs = explode('/', $thisUrl);
            if ($slugs[0] == '')
            {
                $breadcrumbArray = null;
            } else {
                $breadcrumbArray = array();
                $trailLink = '';
                foreach ($slugs as $slug) {
                    $trailLink .= "/" . $slug;
                    $url = $this->_urlTranslate($trailLink, $slug);
                    $title = $this->_titleTranslate($trailLink, $slug);
                    $breadcrumbArray[] = array(
                        'url' => $url,
                        'title' => $title
                    );
                }
                $lastPage = array_pop($breadcrumbArray);
                $breadcrumbArray['currentPage'] = $lastPage['title'];
            }

            // Render breadcrumb trail using partial
            echo $this->view->partial('partials/breadcrumb.phtml', array('breadcrumbArray' => $breadcrumbArray));
        }
    }

    private function _exclude($trail) {

        // Is there a simple path match?
        $pathCheck = array_flip($this->_exclusions['path']);
        if (isset($pathCheck[$trail])) {

            return true;
        }

        // Is there a regex match?
        if (isset($this->_exclusions['regex'])) {

            foreach($this->_exclusions['regex'] as $regex) {

                if (preg_match("/{$regex}/", $trail, $matches) > 0) {

                    return true;
                }
            }
        }

        return false;
    }

    private function _titleTranslate($trail, $slug) {

        // Is there a simple path match?
        if (isset($this->_translations['path'][$trail])) {

            return $this->_translations['path'][$trail];
        }

        // Is there a regex match?
        if (isset($this->_translations['regex'])) {

            foreach($this->_translations['regex'] as $regex => $translation) {

                if (preg_match("/{$regex}/", $trail, $matches) > 0) {

                    return $translation;
                }
            }
        }

        // No matches, base name part on slug
        return ucwords(str_replace('-', ' ', $slug));
    }

    private function _urlTranslate($trail, $slug) {

            // Is there a simple path match?
        if (isset($this->_reverseRewrites['path'][$trail])) {

            return $this->_reverseRewrites['path'][$trail];
        }

        return $trail;
    }
}
