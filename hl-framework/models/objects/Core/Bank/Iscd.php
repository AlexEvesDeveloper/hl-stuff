<?php

/**
 * Represents an ISCD (Industry Sorting Code Directory) object in the system.
 */
class Model_Core_Bank_Iscd extends Model_Abstract {

	public $ISCDid;
	public $sortCode;
	public $BICBank;
	public $BICBranch;
	public $subBranchSuffix;
	public $shortBranchTitle;
	public $shortNameOfOwningBank;
	public $fullOwningBankName1;
	public $fullOwningBankName2;
	public $owningBankCode;
	public $nationalCentralBankCountryCode;
	public $supervisoryBody;
	public $deletedDate;
	public $dateLastChange;
	public $printIndicator;
	public $BACSStatus;
	public $BACSDateLastChange;
	public $dateClosedBACSClearing;
	public $redirectionFromFlag;
	public $redirectedToSortCode;
	public $BACSSettlementBank;
	public $settlementSection;
	public $settlementSubSection;
	public $handlingBank;
	public $handlingBankStream;
	public $accountNumberedFlag;
	public $DDIVoucherFlag;
	public $disallowDR;
	public $disallowCR;
	public $disallowCU;
	public $disallowPR;
	public $disallowBS;
	public $disallowDV;
	public $disallowAU;
	public $disallowX;
	public $disallowY;
	public $disallowZ;
	public $spareField;
	public $CHAPSSterlingReturnIndicator;
	public $CHAPSSterlingStatus;
	public $CHAPSSterlingEffectiveDateLastChange;
	public $CHAPSSterlingDateClosedInClearing;
	public $CHAPSSterlingSettlementMember;
	public $CHAPSSterlingRoutingBICBank;
	public $CHAPSSterlingRoutingBICBranch;
	public $CHAPSEuroStatus;
	public $CHAPSEuroEffectiveDateLastChange;
	public $CHAPSEuroDateClosedInClearing;
	public $CHAPSEuroRoutingBICBank;
	public $CHAPSEuroRoutingBICBranch;
	public $CHAPSEuroSettlementMember;
	public $CHAPSEuroReturnIndicator;
	public $CHAPSEuroSWIFTData;
	public $CHAPSEuroSpareField;
	public $CandCCCStatus;
	public $CandCCCEffectiveDateLastChange;
	public $CandCCCDateClosedInClearing;
	public $CandCCCSettlementBank;
	public $CandCCCDebitAgencySortCode;
	public $CandCCCReturnIndicator;
	public $CandCCCGB_NI_Indicator;
	public $PRINTBranchTypeIndicator;
	public $PRINTSortCodeMainBranch;
	public $PRINTMajorLocationName;
	public $PRINTMinorLocationName;
	public $PRINTBranchNamePlace;
	public $PRINTSecondEntryIndicator;
	public $PRINTBranchNameForSecondEntry;
	public $PRINTBranchTitle1;
	public $PRINTBranchTitle2;
	public $PRINTBranchTitle3;
	public $PRINTAddress1;
	public $PRINTAddress2;
	public $PRINTAddress3;
	public $PRINTAddress4;
	public $PRINTTown;
	public $PRINTCounty;
	public $PRINTPostcodeMajorPart;
	public $PRINTPostcodeMinorPart;
	public $PRINTPhoneArea;
	public $PRINTSubscriberNo;
	public $reservedFAXAreaCode;
	public $reservedFAXSubscriberNo;
}

?>