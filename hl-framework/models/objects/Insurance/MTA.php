<?php

/**
 * Class Model_Insurance_MTA
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_MTA extends Model_Abstract
{
    /**
     * Identifier for status of pending
     */
    const STATUS_PENDING = 'pending';

    /**
     * Identifier for status of awaiting payment
     */
    const STATUS_AWAITING_PAYMENT = 'awaitingPayment';

    /**
     * Identifier for status of live
     */
    const STATUS_LIVE = 'live';

    /**
     * Identifier for status of lapsed
     */
    const STATUS_LAPSED = 'lapsed';

    /**
     * Identifier for status of policy cancelled
     */
    const STATUS_POLICY_CANCELLED = 'policyCancelled';

    /**
     * @var string
     */
    protected $policynumber;

    /**
     * @var string
     */
    protected $policyoptions;

    /**
     * @var string
     */
    protected $amountscovered;

    /**
     * @var string
     */
    protected $optionpremiums;

    /**
     * @var string
     */
    protected $dateAdded;

    /**
     * @var string
     */
    protected $dateOnRisk;

    /**
     * @var string
     */
    protected $dateOffRisk;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $MTAId;

    /**
     * @var float
     */
    protected $premium;

    /**
     * @var float
     */
    protected $quote;

    /**
     * @var float
     */
    protected $ipt;

    /**
     * @var float
     */
    protected $amountToPay;

    /**
     * @var float
     */
    protected $AdminCharge;

    /**
     * @var string
     */
    protected $displayNotes;

    /**
     * @var int
     */
    protected $monthsRemaining;

    /**
     * @var string
     */
    protected $propAddress1;

    /**
     * @var string
     */
    protected $propAddress3;

    /**
     * @var string
     */
    protected $propAddress5;

    /**
     * @var string
     */
    protected $propPostcode;

    /**
     * @var string
     */
    protected $changeCorrespondenceAndPersonal;

    /**
     * @var int
     */
    protected $riskArea;

    /**
     * @var int
     */
    protected $riskAreaB;

    /**
     * @var string
     */
    protected $paidNet;

    /**
     * @var string
     */
    protected $paragonMortgageNumber;

    /**
     * Hydrates the properties from an array
     *
     * @param array $row
     * @return Model_Insurance_MTA
     */
    public static function hydrate(array $row)
    {
        $claims = new self();
        foreach($row as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($claims, $setter)) {
                $claims->{$setter}($value);
            }
        }
        return $claims;
    }

    /**
     * Converts the date to database format
     *
     * @param mixed $inputDate
     * @return string
     */
    private function transformDate($inputDate)
    {
        if (
            null === $inputDate ||
            '00-00-0000' == $inputDate ||
            '0000-00-00' == $inputDate ||
            '' == $inputDate
        ) {
            return '0000-00-00';
        }
        else if ($inputDate instanceof \DateTime) {
            $returnDate = $inputDate->format('Y-m-d');
        }
        else {
            $returnDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));
        }
        return $returnDate;
    }

    /**
     * Gets the admin charge
     *
     * @return float
     */
    public function getAdminCharge()
    {
        return $this->AdminCharge;
    }

    /**
     * Sets the admin charge
     *
     * @param $AdminCharge
     * @return $this
     */
    public function setAdminCharge($AdminCharge)
    {
        $this->AdminCharge = $AdminCharge;
        return $this;
    }

    /**
     * Gets the MTA Id
     *
     * @return int
     */
    public function getMTAId()
    {
        return $this->MTAId;
    }

    /**
     * Sets the MTA Id
     *
     * @param $MTAId
     * @return $this
     */
    public function setMTAId($MTAId)
    {
        $this->MTAId = $MTAId;
        return $this;
    }

    /**
     * Gets the amount to pay
     *
     * @return float
     */
    public function getAmountToPay()
    {
        return $this->amountToPay;
    }

    /**
     * Sets the amount to pay
     *
     * @param $amountToPay
     * @return $this
     */
    public function setAmountToPay($amountToPay)
    {
        $this->amountToPay = $amountToPay;
        return $this;
    }

    /**
     * Gets the amounts covered
     *
     * @return string
     */
    public function getAmountscovered()
    {
        return $this->amountscovered;
    }

    /**
     * Gets the amounts covered
     *
     * @param $amountscovered
     * @return $this
     */
    public function setAmountscovered($amountscovered)
    {
        $this->amountscovered = $amountscovered;
        return $this;
    }

    /**
     * Gets the 'ChangeCorrespondenceAndPersonal' flag ('yes' or '')
     *
     * @return string
     */
    public function getChangeCorrespondenceAndPersonal()
    {
        return $this->changeCorrespondenceAndPersonal;
    }

    /**
     * Sets the 'ChangeCorrespondenceAndPersonal' flag ('yes' or '')
     *
     * @param $changeCorrespondenceAndPersonal
     * @return $this
     */
    public function setChangeCorrespondenceAndPersonal($changeCorrespondenceAndPersonal)
    {
        $this->changeCorrespondenceAndPersonal = $changeCorrespondenceAndPersonal;
        return $this;
    }

    /**
     * Gets the date added string
     *
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Sets the date added string
     *
     * @param $dateAdded
     * @return $this
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $this->transformDate($dateAdded);
        return $this;
    }

    /**
     * Gets the date off risk string
     *
     * @return string
     */
    public function getDateOffRisk()
    {
        return $this->dateOffRisk;
    }

    /**
     * Sets the date off risk string
     *
     * @param $dateOffRisk
     * @return $this
     */
    public function setDateOffRisk($dateOffRisk)
    {
        $this->dateOffRisk = $this->transformDate($dateOffRisk);
        return $this;
    }

    /**
     * Gets the date on risk string
     *
     * @return string
     */
    public function getDateOnRisk()
    {
        return $this->dateOnRisk;
    }

    /**
     * Sets the date on risk string
     *
     * @param $dateOnRisk
     * @return $this
     */
    public function setDateOnRisk($dateOnRisk)
    {
        $this->dateOnRisk = $this->transformDate($dateOnRisk);
        return $this;
    }

    /**
     * Gets the display notes
     *
     * @return string
     */
    public function getDisplayNotes()
    {
        return $this->displayNotes;
    }

    /**
     * Sets the display notes
     *
     * @param $displayNotes
     * @return $this
     */
    public function setDisplayNotes($displayNotes)
    {
        $this->displayNotes = $displayNotes;
        return $this;
    }

    /**
     * Gets the IPT
     *
     * @return float
     */
    public function getIpt()
    {
        return $this->ipt;
    }

    /**
     * Sets the IPT
     *
     * @param $ipt
     * @return $this
     */
    public function setIpt($ipt)
    {
        $this->ipt = $ipt;
        return $this;
    }

    /**
     * Gets the months remaining
     *
     * @return int
     */
    public function getMonthsRemaining()
    {
        return $this->monthsRemaining;
    }

    /**
     * Sets the months remaining
     *
     * @param $monthsRemaining
     * @return $this
     */
    public function setMonthsRemaining($monthsRemaining)
    {
        $this->monthsRemaining = $monthsRemaining;
        return $this;
    }

    /**
     * Gets the option premiums
     *
     * @return string
     */
    public function getOptionPremiums()
    {
        return $this->optionpremiums;
    }

    /**
     * Sets the option premiums
     *
     * @param $optionPremiums
     * @return $this
     */
    public function setOptionPremiums($optionPremiums)
    {
        $this->optionpremiums = $optionPremiums;
        return $this;
    }

    /**
     * Gets the paid net
     *
     * @return string
     */
    public function getPaidNet()
    {
        return $this->paidNet;
    }

    /**
     * Sets the paid net
     *
     * @param $paidNet
     * @return $this
     */
    public function setPaidNet($paidNet)
    {
        $this->paidNet = $paidNet;
        return $this;
    }

    /**
     * Gets the paragon mortgage number
     *
     * @return string
     */
    public function getParagonMortgageNumber()
    {
        return $this->paragonMortgageNumber;
    }

    /**
     * Sets the paragon mortgage number
     *
     * @param $paragonMortgageNumber
     * @return $this
     */
    public function setParagonMortgageNumber($paragonMortgageNumber)
    {
        $this->paragonMortgageNumber = $paragonMortgageNumber;
        return $this;
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policynumber;
    }

    /**
     * Sets the policy number
     *
     * @param $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policynumber = $policyNumber;
        return $this;
    }

    /**
     * Gets the policy options
     *
     * @return string
     */
    public function getPolicyOptions()
    {
        return $this->policyoptions;
    }

    /**
     * Sets the policy options
     *
     * @param $policyOptions
     * @return $this
     */
    public function setPolicyOptions($policyOptions)
    {
        $this->policyoptions = $policyOptions;
        return $this;
    }

    /**
     * Gets the premium
     *
     * @return float
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * Sets the premium
     *
     * @param $premium
     * @return $this
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;
        return $this;
    }

    /**
     * Gets the property address 1
     *
     * @return string
     */
    public function getPropAddress1()
    {
        return $this->propAddress1;
    }

    /**
     * Sets the property address 1
     *
     * @param $propAddress1
     * @return $this
     */
    public function setPropAddress1($propAddress1)
    {
        $this->propAddress1 = $propAddress1;
        return $this;
    }

    /**
     * Gets the property address 3
     *
     * @return string
     */
    public function getPropAddress3()
    {
        return $this->propAddress3;
    }

    /**
     * Sets the property address 3
     *
     * @param $propAddress3
     * @return $this
     */
    public function setPropAddress3($propAddress3)
    {
        $this->propAddress3 = $propAddress3;
        return $this;
    }

    /**
     * Gets the property address 5
     *
     * @return string
     */
    public function getPropAddress5()
    {
        return $this->propAddress5;
    }

    /**
     * Sets the property address 5
     *
     * @param $propAddress5
     * @return $this
     */
    public function setPropAddress5($propAddress5)
    {
        $this->propAddress5 = $propAddress5;
        return $this;
    }

    /**
     * Gets the property postcode
     *
     * @return string
     */
    public function getPropPostcode()
    {
        return $this->propPostcode;
    }

    /**
     * Sets the property postcode
     *
     * @param $propPostcode
     * @return $this
     */
    public function setPropPostcode($propPostcode)
    {
        $this->propPostcode = $propPostcode;
        return $this;
    }

    /**
     * Gets the quote
     *
     * @return float
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Sets the quote
     *
     * @param $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Gets the risk area
     *
     * @return int
     */
    public function getRiskArea()
    {
        return $this->riskArea;
    }

    /**
     * Sets the risk area
     *
     * @param $riskArea
     * @return $this
     */
    public function setRiskArea($riskArea)
    {
        $this->riskArea = $riskArea;
        return $this;
    }

    /**
     * Gets the risk area B
     *
     * @return int
     */
    public function getRiskAreaB()
    {
        return $this->riskAreaB;
    }

    /**
     * Sets the risk area B
     *
     * @param $riskAreaB
     * @return $this
     */
    public function setRiskAreaB($riskAreaB)
    {
        $this->riskAreaB = $riskAreaB;
        return $this;
    }

    /**
     * Gets the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

}