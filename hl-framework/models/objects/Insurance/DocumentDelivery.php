<?php

/**
 * Encapsulates the delivery details of a single quote/policy document.
 */
class Model_Insurance_DocumentDelivery extends Model_Abstract {

	/**#@+
	 * Constants used to specify the target recipient of the document.
	 */
	const HOLDER = 'holder';
	const AGENT = 'agent';
	/**#@-*/
	
	
	/**#@+
	 * Constants used to specify the method of delivery.
	 */
	const POST = 'post';
	const EMAIL = 'email';
	/**#@-*/
	
	
	/**#@+
	 * Constants used to specify the postage.
	 */
	const FIRST_CLASS = 1;
	const SECOND_CLASS = 2;
	/**#@-*/
	
	
	/**
	 * Identifies the document recipient.
	 *
	 * @var integer
	 * If set, then must correspond to self::HOLDER or self::AGENT.
	 */
	public $target;
	
	
	/**
	 * The time the document was queued.
	 *
	 * @var Zend_Date
	 */
	public $timeQueued;
	
	
	/**
	 * The time the document was sent out.
	 * 
	 * @var Zend_Date
	 */
	public $timeSent;
	
	
	/**
	 * How the document was sent.
	 *
	 * @var integer
	 * If set, then must correspond to self::POST or self::EMAIL.
	 */
	public $deliveryType;
	
	
	/**
	 * First or second class.
	 *
	 * @var integer
	 * If set, then must correspond to self::FIRST_CLASS or self::SECOND_CLASS.
	 */
	public $postage;
	
	
	/**
	 * Email to which the document was sent.
	 *
	 * @var string
	 */
	public $emailTo;
}

?>