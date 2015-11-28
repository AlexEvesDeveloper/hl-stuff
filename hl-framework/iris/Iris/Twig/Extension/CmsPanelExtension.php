<?php

namespace Iris\Twig\Extension;
use \Datasource_Cms_Panels;

/**
 * Class CmsPanelExtension
 *
 * @package Iris\Twig\Extension
 * @author Paul Swift <paul.swift@barbon.com>
 */
class CmsPanelExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'iris_cms_zend_panel',
                array(
                    $this,
                    'cmsZendPanelFunction',
                ),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction(
                'iris_cms_silverstripe_panel',
                array(
                    $this,
                    'cmsSilverstripePanelFunction'
                ),
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * Get a CMS panel from the Zend CMS by its key.
     *
     * @param string $panelKey
     * @return string
     */
    public function cmsZendPanelFunction($panelKey)
    {
        $panelDatasource = new Datasource_Cms_Panels();
        $panel = $panelDatasource->getByKey($panelKey);

        if ($panel) {
            return $panel['content'];
        }

        return '';
    }

    /**
     * Get a CMS panel from the Silverstripe CMS by its key.
     *
     * @todo Not implemented yet.
     *
     * @param string $panelKey
     * @return string
     */
    public function cmsSilverstripePanelFunction($panelKey)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_cmsPanel';
    }
}