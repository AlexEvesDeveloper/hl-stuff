<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions;

use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

class DisplayPreferences implements JsonSerializable
{
    /**
     * ﻿Always displayed if defined, and always first.
     */
    const TERMS_ALL = 'all';

    /**
     * ﻿Shown to agents who are initiating agent references.
     */
    const TERMS_AGENT = 'agent';

    /**
     * ﻿Shown to direct landlords who are initiating direct landlord references.
     */
    const TERMS_LANDLORD = 'landlord';

    /**
     * ﻿Seen by the referenced tenant when an agent or direct landlord elects to use the "email to tenant" completion
     * method.
     */
    const TERMS_TENANT = 'tenant';

    /**
     * ﻿Seen by the guarantor when an agent or direct landlord elects to use the "email to tenant" completion method.
     */
    const TERMS_GUARANTOR = 'guarantor';

    /**
     * ﻿For financial referees like employers, pension administrators, accountants, etc.
     */
    const TERMS_REFEREE = 'referee';

    /**
     * ﻿Array key name for the customisable registration header text.
     */
    const CUSTOM_TEXT_REGISTRATION_HEADER = 'customTextRegistrationHeader';

    /**
     * Analytics key for when Google Analytics is used.
     */
    const ANALYTICS_GOOGLE = 'google';

    /**
     * The tracking ID to use for GA.
     */
    const ANALYTICS_GOOGLE_ID = 'trackingId';

    /**
     * The domain to use for cross-domain linking with GA (optional).
     */
    const ANALYTICS_GOOGLE_CROSS_DOMAIN = 'crossDomain';

    /**
     * LESS and CSS declarations that configure basic styling like font faces, sizes, colours, etc.
     *
     * @Iris\Field(optional = true)
     * @var string
     */
    private $style = '';

    /**
     * Associative array of terms and conditions text, with each key being one of self::TERMS_* indicating the type of
     * end user the terms are shown to.  Each are optional.
     *
     * @Iris\Field(optional = true)
     * @var array
     */
    private $terms = [];

    /**
     * Associative array of custom text snippets used throughout the application, with each key being one of self::CUSTOM_TEXT_*
     * indicating the specific piece of text to which it refers.
     *
     * @Iris\Field(optional = true)
     * @var array
     */
    private $customText = [];

    /**
     * @Iris\Field(optional = true)
     * @var array
     */
    private $analytics = [];

    /**
     * Switch for whether to use the branding in the first place.
     *
     * @Iris\Field(optional = true)
     * @var bool
     */
    private $dualBrand = true;

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set style
     *
     * @param string $style
     * @return $this
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Get terms
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set terms
     *
     * @param array $terms
     * @return $this
     */
    public function setTerms(array $terms)
    {
        $this->terms = $terms;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomText()
    {
        return $this->customText;
    }

    /**
     * @param array $customText
     * @return $this
     */
    public function setCustomText(array $customText)
    {
        $this->customText = $customText;
        return $this;
    }

    /**
     * @return array
     */
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * @param array $analytics
     * @return $this
     */
    public function setAnalytics(array $analytics)
    {
        $this->analytics = $analytics;
        return $this;
    }

    /**
     * Get dualBrand
     *
     * @return boolean
     */
    public function getDualBrand()
    {
        return $this->dualBrand;
    }

    /**
     * Set dualBrand
     *
     * @param boolean $dualBrand
     * @return $this
     */
    public function setDualBrand($dualBrand)
    {
        $this->dualBrand = $dualBrand;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'style' => $this->getStyle(),
            'terms' => $this->getTerms(),
            'customText' => $this->getCustomText(),
            'analytics' => $this->getAnalytics(),
            'dualBrand' => $this->getDualBrand()
        ];
    }
}