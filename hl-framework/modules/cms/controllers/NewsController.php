<?php
class Cms_NewsController extends Zend_Controller_Action {

	public function init() {
        // Start the zend layout engine and tell it where we put our layouts
        $session = new Zend_Session_Namespace('homelet_global');
        Zend_Layout::startMvc();
		// Use the CMS layout
		Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH . '/modules/cms/layouts/scripts/' );
        $this->url = $this->getRequest()->getRequestUri();
        // Trim the leading forward slash off
        $this->url = substr($this->url,1);
        
        // Check to see if we have a referrer code - if we do store it in a session variable
        if ($this->getRequest()->getParam('referrer')!='') {
            $session->referrer = $this->getRequest()->getParam('referrer');
        }
        
        // Check to see if we have an agent scheme number - if we do store it in a session variable
        if ($this->getRequest()->getParam('asn')!='') {
            $session->agentSchemeNumber = $this->getRequest()->getParam('asn');
        }
        
        // Populate the menus into the layout
        $menuData = array();
        // This could be quite yucky - I'm just trying to work out how to get the menu structure to work
        if (strpos($this->url,'/')>0) {
            $urlSplit = explode('/',$this->url);
            $menuData['selected'] = $urlSplit[0];
        }
        $menuData['url'] = $this->url;
        
        $params = Zend_Registry::get('params');
        $urlArray = array();
        foreach ($params->url as $key => $url) {
            $urlArray[$key] = $url;
        }
        $menuData['linkUrls'] = $urlArray;
        
        $mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', $menuData);
        $subMenu = $this->view->partial('partials/homelet-submenu.phtml', $menuData);
        $layout = Zend_Layout::getMvcInstance();
        $layout->getView()->mainMenu = $mainMenu;
        $layout->getView()->subMenu = $subMenu;
        $layout->getView()->linkUrls = $urlArray;
        if (isset($menuData['selected'])) { $layout->getView()->styling = $menuData['selected']; }
        
        // Load the site link urls from the parameters and push them into the layout
        $params = Zend_Registry::get('params');
        $layout->getView()->urls = $params->url->toArray();
    }


	/***************************************************************************************/
    /* NEWS PLUGIN FUNCTIONS                                                               */
    /***************************************************************************************/
    
    /**
     * Show a list of the latest news articles
     *
     * @return void
     */
    public function indexAction() {
        $params = Zend_Registry::get('params');
        
        // Work out which category of news we need
        $category = $this->getRequest()->getParam('category');
       	
        if ($category == '') { $category = 'corporate'; }
        
        $news = new Datasource_Cms_News();
        $recentArticles = $news->getRecentByCategory($category, $params->cms->recentNewsLimit);
        $articles = $this->view->partialLoop('partials/news-article-summary.phtml',$recentArticles);
        
        $newsArchives = $news->getArchiveMonths();
        $archives = $this->view->partialLoop('partials/news-archive.phtml', $newsArchives);
        $this->view->content = $articles;
        $this->view->category = $category;
        $this->view->pageTitle = 'News';
        $this->view->archives = $archives;
    }
    
    public function archivesAction() {
        $month = $this->getRequest()->getParam('month');
        $year = $this->getRequest()->getParam('year');
        
        $news = new Datasource_Cms_News();
        $archiveArticles = $news->getArchiveArticles($month, $year);
        $articles = $this->view->partialLoop('partials/news-article-summary.phtml',$archiveArticles);
        
        $newsArchives = $news->getArchiveMonths();
        
        $archives = $this->view->partialLoop('partials/news-archive.phtml', $newsArchives);
        
        $this->view->content = $articles;
        $this->view->pageTitle = 'News Archives';
        $this->view->archives = $archives;
        $date = strtotime($year . '-' . $month . '-01');
        $this->view->month = date('F', $date);
        $this->view->year = date('Y', $date);
    }
    
    
    /**
     * Show a specific news article
     *
     * @return void
     */
    public function articleAction() {
        $articleID = $this->getRequest()->getParam('articleID');
        $news = new Datasource_Cms_News();
        $article = $news->getArticle($articleID);
        
        $content = $article['content'];
        // Replace code snippets in the content
        $snippets = new Application_Cms_PageSnippets();
        $content = $snippets->replace($content);

        $this->view->pageTitle = htmlentities(html_entity_decode($article['title'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        $this->view->content = $content;
        $this->view->title = $article['title'];
        $this->view->summary = $article['summary'];
        $this->view->date = $article['niceDate'];
        $this->view->category = 'corporate';
        $this->view->articleId = intval($articleID);
    }
    
    
    /**
     * Show all news articles in a particular month
     *
     * @return void
     */
    public function archiveAction() {
        $monthFilter = $this->getRequest()->getParam('monthFilter');
    }

}

?>