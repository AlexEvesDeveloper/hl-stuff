<?php

namespace Iris\Utility\DeclarationOwnership;

use Barbondev\IRISSDK\SystemApplication\SystemApplication\SystemApplicationClient;

/**
 * Class DeclarationOwnership 
 *
 * @package Iris\Utility\DeclarationOwnership
 * @author Jun Zhang <jun.zhang@barbon.com>
 */
class DeclarationOwnership
{
    /**
     * Get Agent Scheme Number from different process
     *
     * @param SystemApplicationClient clientContext 
     * @param string linkRef
     * @return string
     */
    public function getAgentSchemeNumberByLinkRef(SystemApplicationClient $clientContext, $linkRef)
    {
        $response = $clientContext->getAgentBranch(array('linkRef' => $linkRef));
        return $response['agentSchemeNumber'];
    }

    /**
     *  
     * @param int agentSchemeNumber
     * @return boolean 
     */
    public function canDisplayDeclaration($agentSchemeNumber)
    {
        $declarationRecord = new \Datasource_ReferencingLegacy_DeclarationDisplay();
        return $declarationRecord->canDisplayDeclaration($agentSchemeNumber);
    }

}
