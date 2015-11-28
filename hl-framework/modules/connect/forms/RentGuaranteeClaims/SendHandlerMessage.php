<?php
class Connect_Form_RentGuaranteeClaims_SendHandlerMessage extends Zend_Form {

    /**
    * Define the Handler Message form elements
    *
    * @return void
    */
    public function init() {
        $this->setMethod('post');
        
        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));
        
        // Add hidden element for claim number
        $this->addElement('hidden', 'claimNumber', array(
            'value' => '',
            'class' => 'noborder'
        ));

       // Add message element
        $this->addElement('textarea', 'message', array(
            'label'     => '',
			'required'   => true,
            'class'  => 'message-text',
			'rows'	=>'5',
			'cols'=>'77',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter message'
                        )
                    )
                )
            )
            ));	
        
       //
       $this->addElement('file','attachdocuments',array(
				'class' 	=> 'multi jquerymultiFile file',
				'id'		=> 'attachdocuments',
				'accept'	=> 'doc|pdf|xls|ppt|docx|xlsx|tif|tiff|txt',
				'size'		=> 43,
                'isArray'	=> true                
			));
        $attachmentDocuments = $this->getElement('attachdocuments');
        $attachmentDocuments->clearDecorators();
        $attachmentDocuments->setDecorators(array('File','Errors'));
       
        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array ('Form'));
        
        // Add the send message button
        $this->addElement('submit', 'send', array(
            'ignore' => true,
            'label' => 'Send Message',
            'class' =>  'btn_orange'
        ));             
        // Nav decorators
        $send = $this->getElement('send');
        $send->clearDecorators();
        $send->setDecorators(array ('ViewHelper'));         
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     * @return bool
     */
    public function isValid($formData = array()) {   
        // validate the file size
        $documents = new Zend_File_Transfer_Adapter_Http();
        $attachDocuments = $documents->getFileInfo();
        $attachmentSize = 0;
        if(count($attachDocuments)>=1) {
           foreach($attachDocuments as $handlerAttachment) {
               if($handlerAttachment['name'] !='') {
                   $attachmentSize += $handlerAttachment['size'];
               }
           }
           if($attachmentSize > $formData['MAX_FILE_SIZE']) {
            $validateDocument = $this->getElement('attachdocuments');
            $validateDocument->addValidator( 'Size', false, array( 
                'max'      => 4194304, 
                'messages' => array(
                    Zend_Validate_File_Size::TOO_BIG => 'Attachment must be no more than 4mb in size'
                )
             ) );
           }
        }    
        // Call original isValid()
        return parent::isValid($formData);
    }

    
}
?>