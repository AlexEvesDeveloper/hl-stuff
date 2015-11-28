<?php

namespace Iris\Referencing\FormSet\ProgressiveStore;

use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Iris\ProgressiveStore\AbstractProgressiveStore;
use Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Referencing\FormSet\Model\AdditionalInformationHolder;

/**
 * Class AgentProgressiveStore
 *
 * @package Iris\Referencing\FormSet\ProgressiveStore
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AgentProgressiveStore extends AbstractProgressiveStore
{
    /**
     * {@inheritdoc}
     */
    public function initialisePrototypes()
    {
        $this
            ->addPrototype(new ReferencingCase())
            ->addPrototype(new ReferencingApplication())
            ->addPrototype(new AdditionalInformationHolder())
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persistPrototypes($class)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
        $case = $this->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase');

        if ('Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase' == $class) {

            if (!$case->getReferencingCaseUuId()) {

                // Creating

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $newCase */
                $newCase = $this
                    ->context
                    ->getReferencingCaseClient()
                    ->createReferencingCase(array(
                        'address' => $case->getAddress(),
                        'totalRent' => $case->getTotalRent(),
                        'tenancyStartDate' => $this->transformDateTime($case->getTenancyStartDate()),
                        'tenancyTerm' => $case->getTenancyTermInMonths(),
                        'numberOfTenants' => $case->getNumberOfTenants(),
                        'propertyType' => $case->getPropertyType(),
                        'propertyLetType' => $case->getPropertyLetType(),
                        'rentGuaranteeOfferingType' => $case->getRentGuaranteeOfferingType(),
                        'prospectiveLandlord' => $case->getProspectiveLandlord(),
                        'propertyBuiltInRangeType' => $case->getPropertyBuiltInRangeType(),
                        'numberOfBedrooms' => (int)$case->getNumberOfBedrooms(),
                    ))
                ;

                $case->setReferencingCaseUuId($newCase->getReferencingCaseUuId());
            }
            else {

                // Updating

                $this
                    ->context
                    ->getReferencingCaseClient()
                    ->updateReferencingCase(array(
                        'referencingCaseUuId' => $case->getReferencingCaseUuId(),
                        'address' => $case->getAddress(),
                        'totalRent' => $case->getTotalRent(),
                        'tenancyStartDate' => $this->transformDateTime($case->getTenancyStartDate()),
                        'tenancyTerm' => $case->getTenancyTermInMonths(),
                        'numberOfTenants' => $case->getNumberOfTenants(),
                        'propertyType' => $case->getPropertyType(),
                        'propertyLetType' => $case->getPropertyLetType(),
                        'rentGuaranteeOfferingType' => $case->getRentGuaranteeOfferingType(),
                        'prospectiveLandlord' => $case->getProspectiveLandlord(),
                        'propertyBuiltInRangeType' => $case->getPropertyBuiltInRangeType(),
                        'numberOfBedrooms' => (int)$case->getNumberOfBedrooms(),
                    ))
                ;

            }
        }


        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        if ('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication' == $class) {

            if (!$application->getReferencingApplicationUuId() && $case->getReferencingCaseUuId()) {

                // Creating

                $addressHistories = $this->getTransformedAddresses($application->getAddressHistories());
                $financialReferees = $this->getTransformedFinancialReferees($application->getFinancialReferees());

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $newApplication */
                $newApplication = $this
                    ->context
                    ->getReferencingApplicationClient()
                    ->createReferencingApplication(array(
                        'referencingCaseUuId' => $case->getReferencingCaseUuId(),
                        'productId' => $application->getProductId(),
                        'title' => $application->getTitle(),
                        'firstName' => $application->getFirstName(),
                        'middleName' => $application->getMiddleName(),
                        'lastName' => $application->getLastName(),
                        'otherName' => $application->getOtherName(),
                        'email' => $application->getEmail(),
                        'birthDate' => $this->transformDateTime($application->getBirthDate()),
                        'employmentStatus' => $application->getEmploymentStatus(),
                        'residentialStatus' => $application->getResidentialStatus(),
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
                        'applicationType' => $application->getApplicationType(),
                        'policyLength' => $application->getPolicyLength(),
                        'canEmploymentChange' => $application->getHasEmploymentChanged(),
                        'canContactApplicantByPhoneAndPost' => $application->getCanContactApplicantByPhoneAndPost(),
                        'canContactApplicantBySMSAndEmail' => $application->getCanContactApplicantBySMSAndEmail(),
                    ))
                ;

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
                        'productId' => $application->getProductId(),
                        'title' => $application->getTitle(),
                        'firstName' => $application->getFirstName(),
                        'middleName' => $application->getMiddleName(),
                        'lastName' => $application->getLastName(),
                        'otherName' => $application->getOtherName(),
                        'email' => $application->getEmail(),
                        'birthDate' => $this->transformDateTime($application->getBirthDate()),
                        'employmentStatus' => $application->getEmploymentStatus(),
                        'residentialStatus' => $application->getResidentialStatus(),
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
                        'applicationType' => $application->getApplicationType(),
                        'policyLength' => $application->getPolicyLength(),
                        'canEmploymentChange' => $application->getHasEmploymentChanged(),
                        'canContactApplicantByPhoneAndPost' => $application->getCanContactApplicantByPhoneAndPost(),
                        'canContactApplicantBySMSAndEmail' => $application->getCanContactApplicantBySMSAndEmail(),
                    ))
                ;

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