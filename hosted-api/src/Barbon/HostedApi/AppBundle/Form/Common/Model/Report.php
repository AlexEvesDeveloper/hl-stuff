<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\Report
 */
class Report
{
    /**
     * @Iris\Body
     * @var string
     */
    private $document;

    /**
     * @Iris\Header(name = "Content-Type")
     * @var string
     */
    private $contentType;

    /**
     * Set content type
     *
     * @param $contentType string
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set the document stream
     *
     * @param string $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }
    
    /**
     * Get the document stream
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Return the report
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->document;
    }
}
