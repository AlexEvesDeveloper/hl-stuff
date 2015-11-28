<?php

class LandlordsInsuranceQuote_Form_CreditCard extends Zend_Form {
    /**
     * CreditCard Form
     *
     * @return void
     */
    public function init()
    {
		$this->addElement('hidden','merchant', array(
            'required'  => true,
            )
		);
                    
		$this->addElement('hidden','trans_id', array(
            'required'  => true,
            )
		);
		
		$this->addElement('hidden','policynumber', array(
            'required'  => true,
            )
        );
		
		$this->addElement('hidden','webleadsummaryid', array(
			'required'	=>	true,
			)
		);
	
		$this->addElement('hidden','refno', array(
            'required'  => true,
            )
        );

		$this->addElement('hidden','paymentfrequency', array(
            'required'  => true,
            )
        );

		$this->addElement('hidden','amount', array(
            'required'  => true,
                )
        );
                    
		$this->addElement('hidden','callback', array(
            'required'  => true,
                )
        );
                    
		$this->addElement('hidden','template', array(
            'required'  => true,
                )
        );

		$this->addElement('hidden','repeat', array(
            'required'  => true,
                )
        );
                    
		$this->addElement('hidden','test_status', array(
            'required'  => true,
                )
        );
                    
		$this->addElement('hidden','test_mpi_status', array(
            'required'  => true,
				)
        );
                    
		$this->addElement('hidden','usage_type', array(
			'required'  => true,
                )
        );
                    
		$this->addElement('hidden','cb_flds', array(
            'required'  => true,
                )
        );
                    
		$this->addElement('hidden','cb_card_type', array(

                )
        );
                    
		$this->addElement('hidden','digest', array(

                )
        );
                    
		$this->addElement('hidden','confirmationcode', array(
                )
        );
                    
		$this->addElement('hidden','dups', array(
                )
		);        
        
		$this->addElement('hidden','backcallback', array(
                )
		);        
        
        $this->addElement('hidden','cb_Post', array(
                )
		); 
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/creditcard.phtml'))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>