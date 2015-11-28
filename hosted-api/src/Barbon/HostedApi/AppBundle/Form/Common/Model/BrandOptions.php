<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions\Behaviours;
use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions\DisplayPreferences;
use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions\Urls;
use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

class BrandOptions implements JsonSerializable
{
    /**
     * @Iris\Field
     * @var DisplayPreferences
     */
    private $displayPreferences;

    /**
     * @Iris\Field(optional = true)
     * @var Behaviours
     */
    private $behaviours;

    /**
     * @Iris\Field(optional = true)
     * @var Urls
     */
    private $urls;

    /**
     * Get displayPreferences
     *
     * @return DisplayPreferences
     */
    public function getDisplayPreferences()
    {
        return $this->displayPreferences;
    }

    /**
     * Set displayPreferences
     *
     * @param DisplayPreferences $displayPreferences
     * @return $this
     */
    public function setDisplayPreferences(DisplayPreferences $displayPreferences)
    {
        $this->displayPreferences = $displayPreferences;
        return $this;
    }

    /**
     * Get behaviours
     *
     * @return Behaviours
     */
    public function getBehaviours()
    {
        return $this->behaviours;
    }

    /**
     * Set behaviours
     *
     * @param Behaviours $behaviours
     * @return $this
     */
    public function setBehaviours(Behaviours $behaviours)
    {
        $this->behaviours = $behaviours;
        return $this;
    }

    /**
     * Get urls
     *
     * @return Urls
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * Set urls
     *
     * @param Urls $urls
     * @return $this
     */
    public function setUrls(Urls $urls)
    {
        $this->urls = $urls;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'displayPreferences' => $this->getDisplayPreferences(),
            'behaviours' => $this->getBehaviours(),
            'urls' => $this->getUrls()
        ];
    }
}