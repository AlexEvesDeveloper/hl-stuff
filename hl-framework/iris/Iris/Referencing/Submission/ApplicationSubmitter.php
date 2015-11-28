<?php

namespace Iris\Referencing\Submission;

use Barbondev\IRISSDK\Common\Exception\DefaultException;

/**
 * Class ApplicationSubmitter
 *
 * @package Iris\Referencing\Submission
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ApplicationSubmitter extends AbstractSubmitter
{
    /**
     * Submit a referencing application
     *
     * @param string $applicationUuId
     * @param string|null $redirectToUrlOnFailure
     * @return bool
     */
    public function submit($applicationUuId, $redirectToUrlOnFailure = null)
    {
        if ($redirectToUrlOnFailure) {
            try {
                /** @var \Guzzle\Http\Message\Response $response */
                $response = $this
                    ->context
                    ->getReferencingApplicationClient()
                    ->submitReferencingApplication(array(
                        'referencingApplicationUuId' => $applicationUuId,
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
                ->getReferencingApplicationClient()
                ->submitReferencingApplication(array(
                    'referencingApplicationUuId' => $applicationUuId,
                ))
            ;
        }

        return in_array($response->getStatusCode(), array(200, 201));
    }
}
