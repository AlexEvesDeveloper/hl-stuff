<?php
class Cms_SitemapController extends Zend_Controller_Action
{
    /**
     * @var array Array of CMS URLs to suppress from the site map.
     */
    private $_suppressCmsUrl = array();

    /**
     * Class constructor.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     *
     * @return void
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array())
    {
        $this->_suppressCmsUrl[] = 'about-us/meet-the-team';

        return parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        // Force response to identify itself as XML, not sure it belongs in a controller
        $this->getResponse()->setHeader('Content-type', 'application/xml');

        $this->_helper->layout->disableLayout();
        $navigation = new Zend_Navigation();

        /* This is going to be hugely dirty. If this still exists in a few months you have my sincere apologies
           I only have a day to write this sitemap and I fully intend to come back and neaten it up at some point */

        // Home
        $page = new Zend_Navigation_Page_Uri();
        $page->label = 'Home';
        $page->uri = '/home';
        $page->changefreq = 'daily';
        $navigation->addPage($page);

        // Careers
        $page = new Zend_Navigation_Page_Uri();
        $page->label = 'Careers';
        $page->uri = '/careers';
        $page->changefreq = 'daily';
        $navigation->addPage($page);

        // News
        $page = new Zend_Navigation_Page_Uri();
        $page->label = 'News';
        $page->uri = '/news';
        $page->changefreq = 'weekly';
        $navigation->addPage($page);

        $page = new Zend_Navigation_Page_Uri();
        $page->label = 'Tenants News';
        $page->uri = '/tenants/news';
        $page->changefreq = 'weekly';
        $navigation->addPage($page);

        $page = new Zend_Navigation_Page_Uri();
        $page->label = 'Landlords News';
        $page->uri = '/landlords/news';
        $page->changefreq = 'weekly';
        $navigation->addPage($page);

        $page = new Zend_Navigation_Page_Uri();
        $page->label = 'Agents News';
        $page->uri = '/letting-agents/news';
        $page->changefreq = 'weekly';
        $navigation->addPage($page);

        $pageModel = new Datasource_Cms_Pages();
        $cmsPages = $pageModel->getPageList();

        foreach($cmsPages as $cmsPage) {
            if (!in_array($cmsPage['url'], $this->_suppressCmsUrl)) {
                $page = new Zend_Navigation_Page_Uri();
                $page->label = $cmsPage['title'];
                $page->uri = '/'.$cmsPage['url'];
                $page->changefreq = 'hourly';
                $navigation->addPage($page);
            }
        }
        $this->view->navigation($navigation);

    }
}
?>