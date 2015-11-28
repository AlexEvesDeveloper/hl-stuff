<?php

/**
 * Class IsCurrentAgentInIris
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Connect_View_Helper_IsCurrentAgentInIris extends Zend_View_Helper_Abstract
{
    /**
     * Is current agent in IRIS?
     *
     * @return bool
     */
    public function isCurrentAgentInIris()
    {
        return (bool) Zend_Auth::getInstance()
            ->getStorage()
            ->read()
            ->isInIris
        ;
    }
}