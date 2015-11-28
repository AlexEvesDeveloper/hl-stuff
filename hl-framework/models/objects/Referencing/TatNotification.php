<?php

/**
 * Represents a TAT notification. A TAT notification occurs when a HRT user emails the reference subject
 * (tenant or guarantor) from the HRT suite. It is comprised of the notification send date and the content
 * of the email.
 */
class Model_Referencing_TatNotification extends Model_Abstract {

    /**
     * The unique identifier for this TAT notification.
     *
     * @var integer
     */
    public $id;
    
    /**
	 * The ERN to which this tat notification is linked.
	 *
	 * The ERN is the unique external Enquiry identifier.
	 *
	 * @var string
	 * E.g. 12345678.1234
	 */
    public $enquiryId;
    
    /**
     * The date on which the notification was sent.
     *
     * @var Zend_Date
     */
    public $sendDate;
    
    /**
     * The content of the email.
     *
     * @var string
     */
    public $content;
}

?>