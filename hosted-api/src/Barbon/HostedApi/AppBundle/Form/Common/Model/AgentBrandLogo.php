<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\AgentBrandLogo
 */
class AgentBrandLogo
{
    /**
     * @Iris\Body
     * @var mixed
     */
    private $logo;

    /**
     * @Iris\Header(name = "Content-Type")
     * @var string
     */
    private $contentType;


    /**
     * Get brand logo
     *
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set brand logo
     *
     * @param mixed $logo
     * @return $this
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get the content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set the content type
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }
}


