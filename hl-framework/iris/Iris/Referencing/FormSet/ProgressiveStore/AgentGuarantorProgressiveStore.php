<?php

namespace Iris\Referencing\FormSet\ProgressiveStore;

use Barbondev\IRISSDK\Common\Enumeration\ReferencingApplicationTypeOptions;
use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Iris\ProgressiveStore\AbstractProgressiveStore;
use Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Referencing\FormSet\Model\AdditionalInformationHolder;

/**
 * Class AgentGuarantorProgressiveStore
 *
 * @package Iris\Referencing\FormSet\ProgressiveStore
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class AgentGuarantorProgressiveStore extends AbstractProgressiveStore
{
    /**
     * @var string
     */
    private $applicantUuid;

    /**
     * {@inheritdoc}
     */
    public function initialisePrototypes()
    {
        $this
            ->addPrototype(new ReferencingApplication())
            ->addPrototype(new AdditionalInformationHolder())
        ;

        return $this;
    }

    /**
     * Set applicant uuid
     *
     * @param string $applicantUuid
     * @return $this
     */
    public function setApplicantUuId($applicantUuid)
    {
        $this->applicantUuid = $applicantUuid;
        return $this;
    }

    /**
     * Get applicant uuid
     *
     * @return string
     */
    public function getApplicantUuId()
    {
        return $this->applicantUuid;
    }

    /**
     * {@inheritdoc}
     */
    public function persistPrototypes($class)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        if ('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication' == $class) {

            if (!$application->getReferencingApplicationUuId()) {

                // Creating

                $addressHistories = $this->getTransformedAddresses($application->getAddressHistories());
                $financialReferees = $this->getTransformedFinancialReferees($application->getFinancialReferees());

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $newApplication */
                $newApplication = $this
                    ->context
                    ->getReferencingApplicationClient()
                    ->createReferencingGuarantorApplication(array(
                        'referencingApplicationUuId' => $this->getApplicantUuId(),
                        'productId' => $application->getProductId(), // TODO: temporary fix to remove errors. Product needs removing (pending API change) as it should be the same as the applicant
                        'title' => $application->getTitle(),
                        'firstName' => $application->getFirstName(),
                        'middleName' => $application->getMiddleName(),
                        'lastName' => $application->getLastName(),
                        'otherName' => $application->getOtherName(),
                        'email' => $application->getEmail(),
                        'birthDate' => $this->transformDateTime($application->getBirthDate()),
                        'employmentStatus' => $application->getEmploymentStatus(),
                        'grossIncome' => $application->getGrossIncome(),
                        'hasCCJ' => $application->getHasCCJ(),
                        'phone' => $application->getPhone(),
                        'mobile' => $application->getMobile(),
                        'bankAccount' => $application->getBankAccount(),
                        'isRentPaidInAdvance' => $application->getIsRentPaidInAdvance(),
                        'financialReferees' => $financialReferees,
                        'addressHistories' => $addressHistories,
                        'rentShare' => $application->getRentShare(),
                        'completionMethod' => $application->getCompletionMethod(),
                        'lettingReferee' => $application->getLettingReferee(),
                        'signaturePreference' => (int)$application->getSignaturePreference(),
                        'applicationType' => ReferencingApplicationTypeOptions::GUARANTOR,
                        'policyLength' => $application->getPolicyLength(), // TODO: temporary fix to remove errors. Policy length needs removing (pending API change) as it should be the same as the applicant
                        'canEmploymentChange' => $application->getHasEmploymentChanged(),
                        'canContactApplicantByPhoneAndPost' => $application->getCanContactApplicantByPhoneAndPost(),
                        'canContactApplicantBySMSAndEmail' => $application->getCanContactApplicantBySMSAndEmail(),
                    ))
                ;

                $application->setApplicationType(ReferencingApplicationTypeOptions::GUARANTOR);

                $application->setReferencingApplicationUuId($newApplication->getReferencingApplicationUuId());

                // If there is no product model in session, try to get one
                if (!$application->getProduct()) {
                    $application->setProduct($this->getProductFromApplication($newApplication->getReferencingApplicationUuId()));
                }

                // If there is no product model in session, try to get one
                if (!$application->getReferenceNumber()) {
                    $application->setReferenceNumber($this->getReferenceNumberFromApplication($newApplication->getReferencingApplicationUuId()));
                }
            }
            else {

                // Updating

                $addressHistories = $this->getTransformedAddresses($application->getAddressHistories());
                $financialReferees = $this->getTransformedFinancialReferees($application->getFinancialReferees());

                $this
                    ->context
                    ->getReferencingApplicationClient()
                    ->updateReferencingApplication(array(
                        'referencingApplicationUuId' => $application->getReferencingApplicationUuId(),
                        'title' => $application->getTitle(),
                        'firstName' => $application->getFirstName(),
                        'middleName' => $application->getMiddleName(),
                        'lastName' => $application->getLastName(),
                        'otherName' => $application->getOtherName(),
                        'email' => $application->getEmail(),
                        'birthDate' => $this->transformDateTime($application->getBirthDate()),
                        'employmentStatus' => $application->getEmploymentStatus(),
                        'grossIncome' => $application->getGrossIncome(),
                        'hasCCJ' => $application->getHasCCJ(),
                        'phone' => $application->getPhone(),
                        'mobile' => $application->getMobile(),
                        'bankAccount' => $application->getBankAccount(),
                        'isRentPaidInAdvance' => $application->getIsRentPaidInAdvance(),
                        'financialReferees' => $financialReferees,
                        'addressHistories' => $addressHistories,
                        'rentShare' => $application->getRentShare(),
                        'completionMethod' => $application->getCompletionMethod(),
                        'lettingReferee' => $application->getLettingReferee(),
                        'signaturePreference' => (int)$application->getSignaturePreference(),
                        'applicationType' => ReferencingApplicationTypeOptions::GUARANTOR,
                        'canEmploymentChange' => $application->getHasEmploymentChanged(),
                        'canContactApplicantByPhoneAndPost' => $application->getCanContactApplicantByPhoneAndPost(),
                        'canContactApplicantBySMSAndEmail' => $application->getCanContactApplicantBySMSAndEmail(),
                    ))
                ;

                $application->setApplicationType(ReferencingApplicationTypeOptions::GUARANTOR);

                // If there is no product model in session, try to get one
                if (!$application->getProduct()) {
                    $application->setProduct($this->getProductFromApplication($application->getReferencingApplicationUuId()));
                }

                // If the product in session is different to that of persisted, update the product
                $product = $application->getProduct();
                if ($product instanceof Product) {
                    if ($application->getProductId() != $product->getId()) {
                        $application->setProduct($this->getProductFromApplication($application->getReferencingApplicationUuId()));
                    }
                }

                // If there is no product model in session, try to get one
                if (!$application->getReferenceNumber()) {
                    $application->setReferenceNumber($this->getReferenceNumberFromApplication($application->getReferencingApplicationUuId()));
                }
            }
        }

    }

    /**
     * Get the product by calling the application
     *
     * @param string $applicationUuId
     * @return \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product
     */
    private function getProductFromApplication($applicationUuId)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->context
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        return $application->getProduct();
    }

    /**
     * Get the reference number by calling the application
     *
     * @param string $applicationUuId
     * @return string
     */
    private function getReferenceNumberFromApplication($applicationUuId)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->context
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        return $application->getReferenceNumber();
    }

    /**
     * Get the addresses transformed for persistence
     *
     * @param array|null $addressHistories
     * @return array|null
     */
    private function getTransformedAddresses($addressHistories)
    {
        if (is_array($addressHistories)) {

            $addressHistoriesTransformed = array();
            foreach ($addressHistories as $addressHistory) {
                $addressHistoryClone = clone $addressHistory;

                // Transform started at date
                $addressHistoryClone->setStartedAt($this->transformDateTime($addressHistoryClone->getStartedAt()));

                $addressHistoriesTransformed[] = $addressHistoryClone;
            }

            return $addressHistoriesTransformed;
        }

        return null;
    }

    /**
     * Get the financial referees transformed for persistence
     *
     * @param array|null $financialReferees
     * @return array|null
     */
    private function getTransformedFinancialReferees($financialReferees)
    {
        if (is_array($financialReferees)) {

            $financialRefereesTransformed = array();
            foreach ($financialReferees as $financialReferee) {

                $financialRefereeClone = clone $financialReferee;

                // Transform employment start date
                $financialRefereeClone->setEmploymentStartDate($this->transformDateTime($financialRefereeClone->getEmploymentStartDate()));
                $financialRefereeClone->setIsPermanent((bool)$financialRefereeClone->getIsPermanent());
                $financialRefereeClone->setAddress($this->isAddressEmpty($financialRefereeClone->getAddress()) ? null : $financialRefereeClone->getAddress());

                $financialRefereesTransformed[] = $financialRefereeClone;
            }

            return $financialRefereesTransformed;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __CLASS__;
    }
}