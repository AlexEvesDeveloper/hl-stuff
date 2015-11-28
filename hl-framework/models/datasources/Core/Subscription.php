<?php

/**
 * Datasource definition for the subscription tables (subscriber,
 * subscriber_type and subscriber_type_map).
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Subscription
 */
class Datasource_Core_Subscription extends Zend_Db_Table_Multidb {

    protected $_name = 'subscriber';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';

    public function fetchById($id) {

        // Attempt to get subscriber + their verified subscriptions by their ID
        /*
         * SELECT s.id AS subscriber_id, s.*, st.id AS subscriber_type_id, st.*
         *   FROM subscriber AS s
         *   LEFT JOIN subscriber_type_map AS stm
         *   ON s.id = stm.subscriber_id
         *   LEFT JOIN subscriber_type AS st
         *   ON stm.subscriber_type_id = st.id
         *   WHERE s.id = '$id';
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('s' => $this->_name),
                array('subscriber_id' => 'id', '*')
            )
            ->joinLeft(
                array('stm' => 'subscriber_type_map'),
                's.id = stm.subscriber_id',
                array('verified')
            )
            ->joinLeft(
                array('st' => 'subscriber_type'),
                'stm.subscriber_type_id = st.id',
                array('subscriber_type_id' => 'id', '*')
            )
            ->where('s.id = ?', $id);

        return $this->_fetchSelected($select, $filterVerified);
    }

    public function fetchByEmail($email, $filterVerified = true) {

        // Attempt to get subscriber + their verified subscriptions by their
        // e-mail address
        /*
         * SELECT s.id AS subscriber_id, s.*, st.id AS subscriber_type_id, st.*
         *   FROM subscriber AS s
         *   LEFT JOIN subscriber_type_map AS stm
         *   ON s.id = stm.subscriber_id
         *   LEFT JOIN subscriber_type AS st
         *   ON stm.subscriber_type_id = st.id
         *   WHERE s.email = '$email';
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('s' => $this->_name),
                array('subscriber_id' => 'id', '*')
            )
            ->joinLeft(
                array('stm' => 'subscriber_type_map'),
                's.id = stm.subscriber_id',
                array('verified')
            )
            ->joinLeft(
                array('st' => 'subscriber_type'),
                'stm.subscriber_type_id = st.id',
                array('subscriber_type_id' => 'id', '*')
            )
            ->where('s.email = ?', $email);

        return $this->_fetchSelected($select, $filterVerified);
    }

    private function _fetchSelected($select, $filterVerified) {

        $subscriberRows = $this->fetchAll($select);

        $returnVal = null;

        if (!is_null($subscriberRows) && $subscriberRows->count() > 0) {

            // User record found, instantiate and populate susbscriber object
            $subscriber = new Model_Core_Subscription_Subscriber();
            $subscriber->id =           $subscriberRows[0]['subscriber_id'];
            $subscriber->email =        $subscriberRows[0]['email'];
            $subscriber->realName =     $subscriberRows[0]['real_name'];
            $subscriber->added =        $subscriberRows[0]['added'];
            $subscriber->updated =      $subscriberRows[0]['updated'];
            $subscriber->newsServices = array();

            // Populate verified news services, possibly filtered to only verified ones
            foreach($subscriberRows as $subscriberRow) {
                if (!$filterVerified || (!is_null($subscriberRow['verified']) && $subscriberRow['verified'] != '0000-00-00 00:00:00')) {
                    $newsService = new Model_Core_Subscription_NewsService();
                    $newsService->id =                          $subscriberRow['subscriber_type_id'];
                    $newsService->type =                        $subscriberRow['type'];
                    $newsService->description =                 $subscriberRow['description'];
                    $newsService->notifySubscriberChangesTo =   $subscriberRow['notify_subscriber_changes_to'];
                    $subscriber->newsServices[] = $newsService;
                }
            }

            $returnVal = $subscriber;
        }

        return $returnVal;
    }

    /**
     * Does not update/insert subscriptions, only the subscriber's personal details.
     *
     * @param $subscriber
     */
    public function upsertSubscriber(Model_Core_Subscription_Subscriber $subscriber) {

        // Check if this should be an UPDATE or an INSERT by checking if record
        // already exists
        $select = $this->select()
            ->from(
                array('s' => $this->_name)
            )
            ->where('s.email = ?', $subscriber->email);
        $lookUp = $this->fetchAll($select);

        if (!is_null($lookUp)) {

            // Do an UPDATE

            // Get record ID
            //$id = $lookUp[''];
        } else {

            // Do an INSERT
        }
    }

    public function subscribe(Model_Core_Subscription_Subscriber $subscriber, $newsService) {

    }

    public function unsubscribe(Model_Core_Subscription_Subscriber $subscriber, $newsService) {

    }

    public function getSubscribers($newsService) {

    }

    protected function _getNewsServices(Model_Core_Subscription_Subscriber $subscriber) {

    }
}