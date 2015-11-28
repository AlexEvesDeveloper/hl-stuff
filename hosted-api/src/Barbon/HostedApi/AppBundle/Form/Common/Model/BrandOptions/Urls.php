<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions;

use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

class Urls implements JsonSerializable
{
    /**
     * For detailed help information hosted on an integrator's website.
     */
    const URL_HELP = 'help';

    /**
     * For all state change, currently not implemented.
     */
    const URL_CALLBACK = 'callback';

    /**
     * Associative array of URLs, with each key being one of self::URL_* indicating the type and use of the URL.  Each
     * are optional.
     *
     * @Iris\Field(optional = true)
     * @var array
     */
    private $urls = [];

    /**
     * Get urls
     *
     * @return array
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * Set urls
     *
     * @param array $urls
     * @return $this
     */
    public function setUrls(array $urls)
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
            'urls' => $this->getUrls()
        ];
    }
}