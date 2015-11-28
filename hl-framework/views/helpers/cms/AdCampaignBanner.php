<?php

/**
 * Class Cms_View_Helper_AdCampaignBanner
 */
class Cms_View_Helper_AdCampaignBanner extends Zend_View_Helper_Abstract
{
    /**
     * @var array Array of banner mappings for each module.
     */
    private $bannerMappings;

    /**
     * @var string Holds current utm_campaign code, if any.
     */
    private $campaign;

    /**
     * @var string Holds current utm_content code, if any.
     */
    private $content;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Banner mappings configuration
        // todo: Belongs elsewhere
        $this->bannerMappings = array(
            'landlords-insurance-quote' => array(
                'campaignA' => array(
                    'soft' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner1.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campA-sft1'
                    ),
                    'spiky' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner2.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campA-spk2'
                    ),
                    'hard' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner3.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campA-hd3'
                    )
                ),
                'campaignB' => array(
                    'soft' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner4.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campB-sft4'
                    ),
                    'spiky' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner5.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campB-spk5'
                    ),
                    'hard' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner6.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campB-hd6'
                    )
                ),
                'campaignC' => array(
                    'soft' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner7.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campC-sft7'
                    ),
                    'spiky' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner8.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campC-spk8'
                    ),
                    'hard' => array(
                        'imagePath' => 'http://homelet.co.uk/assets/landlords-insurance-banner9.png',
                        'imageLink' => null,
                        'imageAlt' => 'Defaqto 5 star rated landlord cover',
                        'additionalText' => 'Find out more',
                        'additionalTextLink' => 'http://homelet.co.uk/landlord-campC-hd9'
                    )
                ),
            )
        );
    }

    /**
     * @param Zend_Controller_Request_Http $request
     * @return null|string
     */
    public function adCampaignBanner(Zend_Controller_Request_Http $request)
    {
        // Get module name from request
        $module = $request->getModuleName();

        // Set or get campaign and content details in session, if any
        $session = new Zend_Session_Namespace('homelet_global');

        if ($request->getParam('utm_campaign') != '') {
            $session->campaign = $request->getParam('utm_campaign');
        }

        if ($request->getParam('utm_content') != '') {
            $session->content = $request->getParam('utm_content');
        }

        $this->campaign = $session->campaign;
        $this->content = $session->content;

        // Look up to see if there's a match between module, campaign and content.
        if (isset($this->bannerMappings[$module][$this->campaign][$this->content])) {

            // Pass the matching parameters to the partial view to display
            $params = $this->bannerMappings[$module][$this->campaign][$this->content];
            return $this->view->partial('partials/ad-campaign-banner.phtml', $params);

        }
        else {

            // No match, no banner
            return null;

        }
    }
}