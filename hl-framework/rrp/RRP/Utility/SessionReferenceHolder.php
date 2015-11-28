<?php

namespace RRP\Utility;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;

/**
 * Class SessionReferenceHolder
 *
 * @package RRP\Utility
 * @author Alex Eves <alex.eves@barbon.com>
 */
class SessionReferenceHolder
{
    /**
     * @var string
     */
    const SESSION_KEY_PREFIX = 'rrp_references';

    /**
     * @var \Zend_Session_Namespace
     */
    protected $session;

    /**
     * SessionReferenceHolder constructor.
     *
     * @param \Zend_Session_Namespace $session
     */
    public function __construct(\Zend_Session_Namespace $session)
    {
        $this->session = $session;
    }

    /**
     * Get a single reference from the array of references in the session.
     *
     * @param $referenceNumber
     * @param $currentAsn
     * @return ReferencingApplication
     */
    public function getReferenceFromSession($referenceNumber, $currentAsn)
    {
        $sessionKey = sprintf('%s.%s', $currentAsn, self::SESSION_KEY_PREFIX);

        if (false === ($references = unserialize($this->session->{$sessionKey}))) {
            // No references in the session.
            return false;
        }

        if ( ! array_key_exists($referenceNumber, $references)) {
            // The reference searched for is not the session.
            return false;
        }

        return $references[$referenceNumber];
    }

    /**
     * Check all references from this session namespace.
     * Return false if empty.
     *
     * @param $currentAsn
     * @return array|bool
     */
    public function getReferencesFromSession($currentAsn)
    {
        $sessionKey = sprintf('%s.%s', $currentAsn, self::SESSION_KEY_PREFIX);

        if (false === ($references = unserialize($this->session->{$sessionKey}))) {
            return false;
        }

        return $references;
    }

    /**
     * Put the Reference into the session.
     *
     * @param ReferencingApplication $reference
     * @param $currentAsn
     */
    public function putReferenceInSession(ReferencingApplication $reference, $currentAsn)
    {
        $sessionKey = sprintf('%s.%s', $currentAsn, self::SESSION_KEY_PREFIX);

        // If session already contains an array of references, add to it, otherwise start with an empty array
        $references = array();
        if (false !== $this->session->{$sessionKey}) {
            // Get the current array of references previously added on this form.
            $references = unserialize($this->session->{$sessionKey});
        }

        // Add this new reference to the array, serialize the array up, put it back into the session.
        $key = $reference->getReferenceNumber();
        $references[$key] = $reference;

        $this->session->{$sessionKey} = serialize($references);
    }
}