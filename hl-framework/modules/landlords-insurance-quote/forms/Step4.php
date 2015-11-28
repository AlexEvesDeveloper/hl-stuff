<?php

class LandlordsInsuranceQuote_Form_Step4 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Landlords Step 4
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_UnderwritingQuestions(), 'subform_underwritingquestions');
    } 
    
    public function applyAnswersLogics() {
    	
    	$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
        $policyNumber = $quoteManager->getLegacyID();
        
        //Retrieve and save the underwriting answers.
    	$answersArray = $this->_getAnswers($policyNumber);
        $answersManager = new Manager_Insurance_Answers();
        foreach($answersArray as $answer) {
            	
            if(!$answersManager->getIsAnswerAlreadyStored($answer)) {
            	
            	$answersManager->insertUnderwritingAnswer($answer);
            }
        }
    }
    
    protected function _getAnswers($policyNumber) {
    	
    	$data = $this->getValues();
    	$answersArray = array();
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(53);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration1']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::INFO_ONLY);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(54);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration2']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::YES);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(55);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration2b']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::YES);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(56);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration2c']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::NO);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(57);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration2d']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::NO);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(58);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration3']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::YES);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(59);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration4']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::YES);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(60);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration6']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::NO);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(61);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration7']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::NO);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;

    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(62);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration8']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::NO);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	$answer = new Model_Insurance_Answer();
    	$answer->setPolicyNumber($policyNumber);
    	$answer->setQuestionNumber(63);
    	$answer->setAnswer($data['subform_underwritingquestions']['declaration9']);
    	$answer->setExpectedAnswer(Model_Insurance_Answer::NO);
    	$answer->setDateAnswered(Zend_Date::now());
    	$answersArray[] = $answer;
    	
    	if(!empty($data['subform_underwritingquestions']['declaration10'])) {
    		
	    	$answer = new Model_Insurance_Answer();
	    	$answer->setPolicyNumber($policyNumber);
	    	$answer->setQuestionNumber(64);
	    	$answer->setAnswer($data['subform_underwritingquestions']['declaration10']);
	    	$answer->setExpectedAnswer(Model_Insurance_Answer::YES);
	    	$answer->setDateAnswered(Zend_Date::now());
	    	$answersArray[] = $answer;
    	}
    	
    	return $answersArray;
    }
    
    
    public function applyAdditionalInformationLogics() {
        
    	$data = $this->getValues();
    	
    	if(empty($data['subform_underwritingquestions']['additional_information'])) {
    		
    		return;
    	}
    	
    	$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
        $policyNumber = $quoteManager->getLegacyID();
        
        $additionalInfo = new Model_Insurance_AdditionalInformation();
        $additionalInfo->setPolicyNumber($policyNumber);
    	$additionalInfo->setAdditionalInformation($data['subform_underwritingquestions']['additional_information']);
    	
        $additionalInfoManager = new Manager_Insurance_AdditionalInformation();
        if(!$additionalInfoManager->getIsAdditionalInformationAlreadyStored($policyNumber)) {
            
            $additionalInfoManager->insertAdditionalInformation($additionalInfo);
        }
    }
    
    
    protected function _getAdditionalInformation() {
    	
    	$data = $this->getValues();
    	if(!empty($data['subform_underwritingquestions']['additional_information'])) {
    		
    	}
    }
}
?>