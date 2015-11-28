<?php

/**
 * Class Manager_Insurance_LegacyQuote
 */
class Manager_Insurance_LegacyQuote
{
    /**
     * @var Datasource_Insurance_LegacyQuotes
     */
    private $_legacyQuotes;

    /**
     * @var Datasource_Insurance_LegacyQuote_PropertyDefinitions
     */
    private $_legacyPropertyDefinitions;

    /**
     * @var Datasource_Insurance_LegacyQuote_Properties
     */
    private $_legacyProperties;

    /**
     * Fetch a quote by its quote number.
     *
     * @param string $quoteNumber
     * @return Model_Insurance_LegacyQuote
     */
    public function getQuoteByPolicyNumber($quoteNumber)
    {
        if (null == $this->_legacyQuotes) {
            $this->_legacyQuotes = new Datasource_Insurance_LegacyQuotes();
        }

        return $this->_legacyQuotes->getByPolicyNumber($quoteNumber);
    }

    /**
     * Fetch one or more quotes by a customer's email address.
     *
     * @param string $email
     * @return array Array of Model_Insurance_LegacyQuote
     */
    public function getQuotesByEmail($email)
    {
        if (null == $this->_legacyQuotes) {
            $this->_legacyQuotes = new Datasource_Insurance_LegacyQuotes();
        }

        return $this->_legacyQuotes->getByEmail($email);
    }

    /**
     * Get the properties of a particular policy.
     *
     * @param string $policyNumber Policy number to retrieve properties of
     * @return array Property list
     */
    public function getProperties($policyNumber)
    {
        if (null == $this->_legacyPropertyDefinitions) {
            $this->_legacyPropertyDefinitions = new Datasource_Insurance_LegacyQuote_PropertyDefinitions();
        }

        if (null == $this->_legacyProperties) {
            $this->_legacyProperties = new Datasource_Insurance_LegacyQuote_Properties();
        }

        $properties = array();
        $propertyDefinitions = $this->_legacyPropertyDefinitions->getPropertyDefinitions();

        foreach ($propertyDefinitions as $propertyDefinition) {
            $property = $this->_legacyProperties->getProperty($policyNumber, $propertyDefinition->policyVariableDefID);

            $properties[$propertyDefinition->policyVariableDefID] = array(
                'propertyId'        => $propertyDefinition->policyVariableDefID,
                'propertyName'      => $propertyDefinition->name,
                'propertyDesc'      => $propertyDefinition->description,
                'propertyTimestamp' => $property->timestamp,
                'propertyValue'     => $property->value,
            );
        }

        return $properties;
    }
}
