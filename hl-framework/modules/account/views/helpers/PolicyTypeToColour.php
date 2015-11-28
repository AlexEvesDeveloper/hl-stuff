<?php

/**
 * View helper to take a policy type code and return a brand colour string
 * 
 * @package Account_View_Helper_PolicyTypeToColour
 */
class Account_View_Helper_PolicyTypeToColour extends Zend_View_Helper_Abstract
{
    /**
     * Take a policy type code and return a brand colour string
     *
     * @param string $policyType
     * @return string
     */
    public function policyTypeToColour($policyType)
    {
        if ('T' == $policyType) {
            return 'tertiary';
        } elseif ('L' == $policyType) {
            return 'quaternary';
        }

        return 'primary';
    }
}