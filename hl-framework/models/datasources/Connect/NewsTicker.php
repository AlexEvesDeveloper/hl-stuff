<?php
/**
 * Datasource definition for the internal news ticker.
 */
class Datasource_Connect_NewsTicker extends Zend_Db_Table_Multidb {
    protected $_name = 'news_ticker';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_connect';

    /**
     * This function will return an array of all newsticker items
     *
     * @return array
     *
     */
    public function getAll() {
        $select = $this->select();
        $select->from($this->_name, array('id', 'url', 'news'));
        $newsItems = $this->fetchAll($select);
        $returnArray = array();
        foreach ($newsItems as $newsItem) {
            $returnArray[] = array(
                'id'    =>  $newsItem->id,
                'url'   =>  $newsItem->url,
                'news'  =>  $newsItem->news
            );
        }
        return $returnArray;
    }
}
?>