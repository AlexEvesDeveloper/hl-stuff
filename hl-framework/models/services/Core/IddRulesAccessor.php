<?php

/**
 * Class for fetching IDD rules
 *
 */
class Service_Core_IddRulesAccessor
{

    private $_iddRules;

    /**
     * Set up XML rules object.
     *
     * @return void
     */
    public function __construct()
    {
        // Load up and parse XML rules from iddRules.xml
        $this->_iddRules = simplexml_load_file(APPLICATION_PATH . '/configs/rulesets/iddRules.xml');
    }

    /**
     * Given the filters, what IDD (if any) should be shown/sent.  Typical
     *   values for the filters can be seen in the config XML.  Note that
     *   evaluation continues after a match, allowing for cascaded rules.
     *
     * @param string $fsaStatus The FSA status of the associated agent, should be lower case
     * @param string $quoteBy The method by which the quote was originally generated
     * @param string $buyBy The method by which the quote is being bought/converted
     * @param string $condition Optional condition that may have had to be met
     *
     * @return string The type of IDD to show/send, or empty for none
     */
    public function fetchIddType($fsaStatus, $quoteBy, $buyBy, $condition = '')
    {
        $result = '';

        // Iterate through rulesets looking for one that applies to the given FSA status
        foreach ($this->_iddRules as $key => $ruleset) {
            // Put the ruleset's for attribute contents into an array
            $for = explode(' ', (string) $ruleset['for']);
            // Does it match up with the FSA status?
            if (in_array($fsaStatus, $for)) {

                // Iterate through quote methods looking for one that matches the given quote method
                foreach ($ruleset as $key => $quote) {
                    // Put the quote's by attribute contents into an array
                    $by1 = explode(' ', (string) $quote['by']);
                    // Does it match up with the quote method?
                    if (in_array($quoteBy, $by1)) {

                        // Iterate through buy methods looking for one that matches the given buy method
                        foreach ($quote as $key => $buy) {
                            // Put the buy's by attribute contents into an array
                            $by2 = explode(' ', (string) $buy['by']);
                            // Does it match up with the buy method?
                            if (in_array($buyBy, $by2)) {

                                // Check if a rule precondition must be met
                                if (!isset($buy['precondition']) || (string) $buy['precondition'] == $condition) {
                                    // No rules needed or rule is met, we have an answer
                                    $result = (string) $buy;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

}