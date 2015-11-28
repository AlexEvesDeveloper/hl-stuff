<?php
/**
* Model definition for the cms news table
* 
*/
class Datasource_Cms_News extends Zend_Db_Table_Multidb {
    protected $_name = 'news';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    /**
     * Return all news articles sorted by date/time
     *
     * @return array
     *
     */
    public function getAll() {
        $params = Zend_Registry::get('params');
        
        $select = $this->select();
        $select->order('date DESC');
        $rows = $this->fetchAll($select);
        $articleArray = $rows->toArray();
        
        $returnArray = array();
        $stripTags = new Zend_Filter_StripTags(array('br'));
        foreach ($articleArray as $article) {
            array_push($returnArray, array(
                'id'        =>  $article['id'],
                'title'     =>  $article['title'],
                'summary'   =>  $stripTags->filter(Application_Core_Utilities::word_split($article['content'], $params->cms->newsSummaryWordLimit)) . ' &hellip;',
                'content'   =>  $article['content'],
                'niceDate'  =>  date('M jS, Y',strtotime($article['date'])),
                'date'      =>  $article['date']
            ));
        }
        
        return $returnArray;
    }
    
    /**
     * Returns a list of recent news articles
     * 
     * @param int limit
     * @return array
     *
     */
    public function getRecent($limit) {
        $params = Zend_Registry::get('params');
        
        $select = $this->select();
        $select->order('date DESC');
        $select->limit($limit);
        $rows = $this->fetchAll($select);
        $articleArray = $rows->toArray();
        
        $returnArray = array();
        $stripTags = new Zend_Filter_StripTags(array('br'));
        foreach ($articleArray as $article) {
            array_push($returnArray, array(
                'id'        =>  $article['id'],
                'title'     =>  $article['title'],
                'summary'   =>  $stripTags->filter(Application_Core_Utilities::word_split($article['content'], $params->cms->newsSummaryWordLimit)) . ' &hellip;',
                'content'   =>  $article['content'],
                'niceDate'  =>  date('M jS, Y',strtotime($article['date'])),
                'date'      =>  $article['date']
            ));
        }
        
        return $returnArray;
    }
    
    public function getRecentByCategory($category, $limit) {
        $params = Zend_Registry::get('params');
        
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('nc' => 'news_categories'), array());
        $select->joinInner(array('ncm' => 'news_categories_map'), 'ncm.news_category_id = nc.id', array());
        $select->joinInner(array('n' => 'news'), 'n.id = ncm.news_id', array('id', 'title', 'content', 'date'));
        $select->where('nc.category = ?',$category);
        $select->order(array('date DESC','id DESC'));
        $select->limit($limit);
        $newsArticles = $this->fetchAll($select);
        
        $returnArray = array();
        $stripTags = new Zend_Filter_StripTags(array('br'));
        foreach ($newsArticles as $newsArticle) {
            array_push($returnArray, array(
                'id'        =>  $newsArticle->id,
                'title'     =>  $newsArticle->title,
                'summary'   =>  $stripTags->filter(Application_Core_Utilities::word_split($newsArticle->content, $params->cms->newsSummaryWordLimit)) . ' &hellip;',
                'content'   =>  $newsArticle->content,
                'niceDate'  =>  date('M jS, Y',strtotime($newsArticle->date)),
                'date'      =>  $newsArticle->date
            ));
        }
        
        /*
         SELECT n.id, n.title, n.content, n.date
         FROM news_categories AS nc
         INNER JOIN news_category_map AS ncm ON ncm.news_category_id = nc.id
         INNER JOIN news AS n ON n.id = ncm.news_id
         WHERE nc.category = $category
        */
        
        return $returnArray;
    }
    
    /**
     * Return a specific news article
     *
     * @param int articleID
     * @return array
     *
     */
    public function getArticle($articleID) {
        $params = Zend_Registry::get('params');

        $select = $this->select();
        $select->where('id = ?', $articleID);
        $article = $this->fetchRow($select);
        
        // Also need to get a list of categories it is linked to
        // Now we have the testimonial - we need to build a list of tags it's currently linked to
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('nc' => 'news_categories'), array('id','category'));
        $select->joinInner(array('ncm' => 'news_categories_map'), 'ncm.news_category_id = nc.id', array());
        $select->where('ncm.news_id = ?', $article->id);
        
        $categories = $this->fetchAll($select);
        $categoryList = '';
        $categoryNames = '';
        foreach ($categories as $category) {
            $categoryList .= $category->id . ',';
            $categoryNames .= $category->category . ',';
        }

        $stripTags = new Zend_Filter_StripTags(array('br'));

        $returnArray = array(
            'id'            =>  $article->id,
            'title'         =>  $article->title,
            'summary'       =>  trim($stripTags->filter(Application_Core_Utilities::word_split($article->content, $params->cms->newsSummaryWordLimit)) . ' &hellip;'),
            'content'       =>  $article->content,
            'date'          =>  $article->date,
            'niceDate'      =>  date('M jS, Y',strtotime($article->date)),
            'categoryList'  =>  $categoryList,
            'categoryNames' =>  $categoryNames
        );
        
        return $returnArray;
    }
    
    
    
    public function getArchiveArticles($month, $year) {
        $params = Zend_Registry::get('params');
        
        $select = $this->select();
        $select->where('date >= ?', date('Y-m-d',strtotime($year.'-'.$month.'-01')));
        $select->where('date < ?', date('Y-m-d',strtotime($year.'-'.$month.'-01 + 1 month')));
        $rows = $this->fetchAll($select);
        $articleArray = $rows->toArray();
        
        $returnArray = array();
        $stripTags = new Zend_Filter_StripTags(array('br'));
        foreach ($articleArray as $article) {
            array_push($returnArray, array(
                'id'        =>  $article['id'],
                'title'     =>  $article['title'],
                'summary'   =>  $stripTags->filter(Application_Core_Utilities::word_split($article['content'], $params->cms->newsSummaryWordLimit)) . ' &hellip;',
                'content'   =>  $article['content'],
                'niceDate'  =>  date('M jS, Y',strtotime($article['date'])),
                'date'      =>  $article['date']
            ));
        }
        
        return $returnArray;
    }
    
    
    public function getArchiveMonths() {
        $select = $this->select();
        $select->from($this->_name, array(
            'month_year' => 'date_format(date, "%M %Y")',
            'url_month_year' => 'date_format(date, "%m/%Y")'
        ));
        $select->group('month_year');
        $select->order('date DESC');
        
        $rows = $this->fetchAll($select);
        $returnArray = array();
        
        foreach ($rows as $row) {
            array_push($returnArray, array(
                'monthYearText' => $row->month_year,
                'urlMonthYear'  => $row->url_month_year
            ));
        }
        
        return $returnArray;
    }
    
    
    
    
    /**
     * Saves categories against a news article
     *
     * @param int newsID
     * @return boolean
     *
     */
    public function saveCategories($newsID, $categoryList) {
        $newsCategories = new Datasource_Cms_News_CategoriesMap();
        $where = $newsCategories->getAdapter()->quoteInto('news_id = ?', $newsID);
        $newsCategories->delete($where);
        
        $categoryList = trim($categoryList,',');
        $categoryList = trim($categoryList);
        $categoryArray = explode(',',$categoryList);
        
        foreach ($categoryArray as $categoryID)
        {
            $data = array (
                'news_id'           =>  $newsID,
                'news_category_id'  =>  $categoryID
            );
            
            $newsCategories->insert($data);
        }
        
        return true;
    }
    
    
    
    /**
     * Save changes to a news article
     *
     * @param int articleID
     * @param string title
     * @param string date (dd/mm/yyyy)
     * @param string content
     *
     */
    public function saveChanges($articleID, $title, $date, $content) {
        $dbDate = new Zend_Date($date);
        
        $data = array(
            'id'            =>  $articleID,
            'title'         =>  $title,
            'date'          =>  $dbDate->toString('YYYY-MM-dd'),
            'content'       =>  $content,
        );
        
        $where = $this->quoteInto('id = ?', $articleID);
        $this->update($data, $where);
    }
    
    
    /**
     * Add a new article. Returns the ID of the new article
     *
     * @param string title
     * @param string date
     * @param string content
     * @return int
     *
     */
    public function addNew($title, $date, $content) {
        $dbDate = new Zend_Date($date);
        
        $data = array(
            'title'     =>  $title,
            'date'      =>  $dbDate->toString('YYYY-MM-dd'),
            'content'   =>  $content
        );
        
        return $this->insert($data);
    }
    
    
    /**
     * This function will delete an existing news article
     *
     * @param int articleID
     *
     */
    public function remove($articleID) {
        $where = $this->quoteInto('id = ?', $articleID);
        $this->delete($where);
    }
}
?>