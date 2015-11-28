<?php

namespace Iris\Referencing\Submission;

use Barbondev\IRISSDK\Common\Exception\DefaultException;

/**
 * Class CaseSubmitter
 *
 * @package Iris\Referencing\Submission
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class CaseSubmitter extends AbstractSubmitter
{
    /**
     * Submit a referencing case
     *
     * @param string $caseUuId
     * @param string|null $redirectToUrlOnFailure
     * @return bool
     */
    public function submit($caseUuId, $redirectToUrlOnFailure = null)
    {
        if ($redirectToUrlOnFailure) {
            try {
                /** @var \Guzzle\Http\Message\Response $response */
                $response = $this
                    ->context
                    ->getReferencingCaseClient()
                    ->submitReferencingCase(array(
                        'referencingCaseUuId' => $caseUuId,
                    ))
                ;
            }
            catch (DefaultException $e) {
                $this->handleErrorOnSubmission($e, $redirectToUrlOnFailure);
            }
        }
        else {
            /** @var \Guzzle\Http\Message\Response $response */
            $response = $this
                ->context
                ->getReferencingCaseClient()
                ->submitReferencingCase(array(
                    'referencingCaseUuId' => $caseUuId,
                ))
            ;
        }

        return in_array($response->getStatusCode(), array(200, 201));
    }
}
