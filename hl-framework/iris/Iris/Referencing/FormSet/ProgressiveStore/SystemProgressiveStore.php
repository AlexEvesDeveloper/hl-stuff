<?php

namespace Iris\Referencing\FormSet\ProgressiveStore;

use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Iris\ProgressiveStore\AbstractProgressiveStore;
use Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Referencing\FormSet\Model\AdditionalInformationHolder;
use Iris\Referencing\FormSet\Model\LinkRefHolder;

/**
 * Class SystemProgressiveStore
 *
 * @package Iris\Referencing\FormSet\ProgressiveStore
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class SystemProgressiveStore extends AbstractProgressiveStore
{
    /**
     * {@inheritdoc}
     */
    public function initialisePrototypes()
    {
        $this
            ->addPrototype(new ReferencingCase())
            ->addPrototype(new ReferencingApplication())
            ->addPrototype(new LinkRefHolder())
            ->addPrototype(new AdditionalInformationHolder())
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persistPrototypes($class)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        /** @var  $linkRefHolder \Iris\Referencing\FormSet\Model\LinkRefHolder */
        $linkRefHolder = $this->getPrototypeByClass('Iris\Referencing\FormSet\Model\LinkRefHolder');

        if ('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication' == $class) {

            // Updating

            $addressHistories = $this->getTransformedAddresses($application->getAddressHistories());
            $financialReferees = $this->getTransformedFinancialReferees($application->getFinancialReferees());

            $this
                ->context
                ->getSystemApplicationClient()
                ->updateApplicant(array(
                    'linkRef' => $linkRefHolder->getLinkRef(),
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
                    'applicationType' => $application->getApplicationType(),
                    'canEmploymentChange' => $application->getHasEmploymentChanged(),
                    'signaturePreference' => (int)$application->getSignaturePreference(),
                    'canContactApplicantByPhoneAndPost' => $application->getCanContactApplicantByPhoneAndPost(),
                    'canContactApplicantBySMSAndEmail' => $application->getCanContactApplicantBySMSAndEmail(),
                ))
            ;

            // If there is no product model in session, try to get one
            if (!$application->getProduct()) {
                $application->setProduct($this->getProductFromApplication($linkRefHolder->getLinkRef()));
            }

            // If the product in session is different to that of persisted, update the product
            $product = $application->getProduct();
            if ($product instanceof Product) {
                if ($application->getProductId() != $product->getId()) {
                    $application->setProduct($this->getProductFromApplication($linkRefHolder->getLinkRef()));
                }
            }

            // If there is no product model in session, try to get one
            if (!$application->getReferenceNumber()) {
                $application->setReferenceNumber($this->getReferenceNumberFromApplication($linkRefHolder->getLinkRef()));
            }
        }

    }

    /**
     * Get the product by calling the application
     *
     * @param string $linkRef
     * @return \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product
     */
    private function getProductFromApplication($linkRef)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->context
            ->getSystemApplicationClient()
            ->getReferencingApplication(array(
                'linkRef' => $linkRef,
            ))
        ;

        return $application->getProduct();
    }

    /**
     * Get the reference number by calling the application
     *
     * @param string $linkRef
     * @return string
     */
    private function getReferenceNumberFromApplication($linkRef)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->context
            ->getSystemApplicationClient()
            ->getReferencingApplication(array(
                'linkRef' => $linkRef,
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