<?php
class Cmsadmin_NewsController extends Zend_Controller_Action {
	public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
    }
    
    /***************************************************************************************/
    /* NEWS FUNCTIONS                                                                      */
    /***************************************************************************************/

    /**
     * Show a list of news articles in the admin system
     *
     * @return void
     */
    public function indexAction() {
        $this->view->currentPage = 'news';
        $news = new Datasource_Cms_News();
        $articlesArray = $news->getAll();

        $this->view->articleList = $this->view->partialLoop('partials/news-row.phtml', $articlesArray);
    }


    /**
     * Add a news article
     *
     * @return void
     */
    public function addAction() {
        if ($this->getRequest()->isPost()) {
            $this->_saveNewsArticle();
        }

        $articleDate = new Zend_Date();

        $this->view->newsDate = $articleDate->toString('dd/MM/YYYY');

        $newsCategories = new Datasource_Cms_News_Categories();
        $newsCategoriesArray = $newsCategories->getAll();

        $this->view->categoryList = $this->view->partialLoop('/partials/news-category.phtml', $newsCategoriesArray);
    }


    /**
     * Delete an existing news article
     *
     * @return void
     */
    public function deleteAction() {
        $articleID = $this->getRequest()->getParam('id');

        $this->view->currentPage = 'news';
        $newsArticle = new Datasource_Cms_News();
        $news = $newsArticle->getArticle($articleID);
        $newsArticle->remove($articleID);

		// Record activity
		$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        $username = $auth->getStorage()->read()->username;
        Application_Core_ActivityLogger::log('CMS News Article Deleted', 'complete', 'CMS-Admin', $username, "News Article Title: ". $news['title']);
        
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/news');
    }


    /**
     * Edit an existing news article
     *
     * @return void
     */
    public function editAction() {
        $this->view->currentPage = 'news';

        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveNewsArticle();
        }

        $newsID = $this->getRequest()->getParam('id');
        $news = new Datasource_Cms_News();
        $article = $news->getArticle($newsID);
        $newsDate = new Zend_Date($article['date']);

        $this->view->newsTitle = $article['title'];
        $this->view->newsContent = $article['content'];
        $this->view->newsID = $newsID;
        $this->view->newsDate = $newsDate->toString('dd/MM/YYYY');
        $this->view->selectedCategories = $article['categoryList'];

        $newsCategories = new Datasource_Cms_News_Categories();
        $newsCategoriesArray = $newsCategories->getAll();
        $selectedCategoryArray = explode(',',$article['categoryList']);

        foreach ($newsCategoriesArray as &$category) {
            if (in_array($category['categoryID'],$selectedCategoryArray)) {
                $category['selected'] = true;
            } else {
                $category['selected'] = false;
            }
        }

        $this->view->categoryList = $this->view->partialLoop('/partials/news-category.phtml', $newsCategoriesArray);

        $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (count($passThrough)>0) {
            if (isset($passThrough[0]['saved'])) {
                if ($passThrough[0]['saved'] == true) $this->view->saved=true;
            }
            if (isset($passThrough[0]['errorMessage'])) {
                $this->view->errorMessage = $passThrough[0]['errorMessage'];
            }
        }
    }


    /**
     * Save changes to an existing news article, or save a new article in the database. If a new article the function will return the ID.
     *
     * @return int
     */
    private function _saveNewsArticle() {
        // First of all we need to validate and sanitise the input from the form
        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty);
        // $requiredText->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)));

        $filters = array(
            'id'                =>  'Digits',
            'newsTitle'         =>  'StringTrim',
            'newsDate'          =>  'StringTrim',
            'categoryList'      =>  'StringTrim'
        );
        $validators = array(
            'id'            =>  array('allowEmpty'  =>  true),
            'newsTitle'     =>  $requiredText,
            'newsContent'   =>  array('allowEmpty'  =>  true),
            'newsDate'      =>  'NotEmpty',
            'categoryList'  =>  array('allowEmpty'  =>  true)
        );

        $input = new Zend_Filter_Input($filters, $validators, $_POST);
        if ($input->isValid()) {
            // Data is all valid, formatted and sanitized so we can save it in the database
            $newsArticle = new Datasource_Cms_News();

            if (!$input->id) {
                // This is a new article so we need to create a new ID
                $newsID = $newsArticle->addNew($input->newsTitle, $input->newsDate, $input->getUnescaped('newsContent'));
            } else {
                // This is an existing article so we can just update the data
                $newsArticle->saveChanges($input->id, $input->newsTitle, $input->newsDate, $input->getUnescaped('newsContent'));
                $newsID = $input->id;
            }

            // Now we need to link the page to the categories selected
            $categoryList = $input->categoryList;
            $newsArticle->saveCategories($newsID, $categoryList);

            // Changes saved - so send them back with a nice success message
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
            $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/news/edit?id='.$newsID);
        } else {
            // Invalid data in form
            print_r($_POST);
            print_r($input->getErrors());
            print_r($input->getInvalid());
        }
    }

}
?>