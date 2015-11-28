<?php

namespace Barbon\HostedApi\AppBundle\Controller\Brand;

use Assetic\Asset\FileAsset;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\LessphpFilter;
use Barbon\HostedApi\AppBundle\Service\Brand\SystemBrand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class StyleController
 *
 * @Route(service="barbon.hosted_api.app.controller.brand.style_controller") 
 *
 * @package Barbon\HostedApi\AppBundle\Brand\Controller
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
final class StyleController extends AbstractBrandController
{
    /**
     * @var string
     */
    private $defaultStyle;

    /**
     * Constructor
     *
     * @param SystemBrand $systemBrandService
     * @param int $httpCacheMaxAgeInSeconds
     * @param string $defaultStyle
     * @internal param IrisEntityManager $irisEntityManager
     * @internal param Cache $cache
     */
    public function __construct(
        SystemBrand $systemBrandService,
        $httpCacheMaxAgeInSeconds = 3600,
        $defaultStyle = "@brandprimary: #0093d0; @pagebackground: #dee6e8; @headingfont: bliss_2light, Arial, sans-serif; @bodyfont: pt_sansregular, Helvetica, Arial, sans-serif;"
    )
    {
        parent::__construct($systemBrandService, $httpCacheMaxAgeInSeconds);

        $this->defaultStyle = $defaultStyle;
    }

    /**
     * Integrator brand stylesheet.
     *
     * @Route("/style")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // Get vendor key
        $vendorKey = $this->getVendorCredentials()['vendorKey'];

        $d = DIRECTORY_SEPARATOR;

        // Get the system web directory
        $sysDir = $this->container->get('kernel')->getRootdir() . "{$d}..{$d}web{$d}";

        // Get the style sheet input path to the uncompiled (raw from IRIS) vendor CSS file
        $sysPathUncompiled = "{$sysDir}css{$d}integrator{$d}uncompiled{$d}{$vendorKey}.less";

        // Get the style sheet output path to the compiled (post Assetic) vendor CSS file
        $sysPathCompiled = "{$sysDir}css{$d}integrator{$d}compiled{$d}{$vendorKey}.css";

        $compiledCss = '';

        // Does a compiled CSS file exist for it that is also under x seconds old?
        if ( ! file_exists($sysPathCompiled) || $this->httpCacheMaxAgeInSeconds < filemtime($sysPathCompiled)) {

            // No CSS file exists or it's stale, make a new one

            // Get vendor style config, import the main style into it, and store it to disc in the uncompiled directory
            $vendorStyle = $this->getVendorStyle();
            $vendorStyle .= "\n\n@import '../style.less';"; // todo Make better
            file_put_contents($sysPathUncompiled, $vendorStyle);

            /** @var CssRewriteFilter $asseticFilterCssRewrite */
            $asseticFilterCssRewrite = $this->container->get('assetic.filter.cssrewrite');

            /** @var LessphpFilter $asseticFilterLessPhp */
            $asseticFilterLessPhp = $this->container->get('assetic.filter.lessphp');
            $asseticFilterLessPhp->setFormatter('classic');
            $asseticFilterLessPhp->setPreserveComments(false);

            $style = new FileAsset(
                $sysPathUncompiled,
                array(
                    $asseticFilterCssRewrite,
                    $asseticFilterLessPhp
                )
            );

            // Dump compiled CSS to file
            $compiledCss = $style->dump();
            file_put_contents($sysPathCompiled, $compiledCss);

        }
        else {

            // Grab compiled CSS from file
            $compiledCss = file_get_contents($sysPathCompiled);

        }

        // Output compiled CSS
        $this->response->headers->set('Content-Type', 'text/css');
        $this->response->setContent($compiledCss);

        return $this->response;
    }

    /**
     * Gets the vendor's style from IRIS, if it exists, and appends it to the defaults otherwise fall back to defaults
     * only.
     *
     * @return string
     */
    private function getVendorStyle()
    {
        $style = $this->defaultStyle;

        $options = $this->getSystemBrandOptions();
        if ( ! $options) {
            // No brand options found for this vendor, return default styles.
            return $style;
        }

        $displayPreferences = $options->getDisplayPreferences();
        if ( ! $displayPreferences) {
            // No display preferences found for this vendor, return default styles.
            return $style;
        }

        $customStyle = $displayPreferences->getStyle();
        if ('' == $customStyle) {
            // No custom styles found for this vendor, return default styles.
            return $style;
        }

        // We found some custom styles, so append them to the default styles.
        return sprintf("%s\n%s", $style, $customStyle);
    }
}