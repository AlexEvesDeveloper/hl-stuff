<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Model;

use Barbon\HostedApi\AppBundle\Form\Common\Model\BankAccount;
use Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee;
use Barbon\HostedApi\AppBundle\Form\Common\Model\PreviousAddress;
use Barbon\IrisRestClient\Annotation as Iris;
use DateTime;

/**
 * @Iris\Entity\ReferencingGuarantor
 */
final class ReferencingGuarantor extends AbstractReferencingApplication
{

}
