<?php

/**
 * Class Model_Insurance_RentRecoveryPlus_AbstractResponseModel
 *
 * @author April Portus <april.portus@barbon.com>
 */
abstract class Model_Insurance_RentRecoveryPlus_AbstractResponseModel
{
    /**
     * Try to hydrate a response model with response data
     *
     * Example mapping definition
     * <code>
     * Array (
     *     responseDataKey => modelPropertyName,
     * )
     * </code>
     *
     * @param object $responseModel
     * @param array $data Response data
     * @param array $mapping Mapping describing model properties that are different to response data properties
     * @param array $defaults Default values for properties
     * @return object
     */
    protected static function hydrateModelProperties(
        $responseModel, array $data, array $mapping = array(), array $defaults = array())
    {
        $reflection = new \ReflectionObject($responseModel);

        foreach ($reflection->getProperties() as $property) {

            $dataKey = array_search($property->getName(), $mapping) ?: $property->getName();

            if (isset($defaults[$dataKey]) || isset($data[$dataKey])) {

                $setterName = sprintf('set%s', ucfirst($property->getName()));

                if (method_exists($responseModel, $setterName)) {

                    if (isset($data[$dataKey])) {
                        $responseModel->{$setterName}($data[$dataKey]);
                    }
                    else if (isset($defaults[$dataKey]) && !empty($defaults[$dataKey])) {
                        $responseModel->{$setterName}($defaults[$dataKey]);
                    }
                }
            }
        }

        return $responseModel;
    }

    /**
     * Returns TRUE if the response data is in an indexed array
     *
     * @param mixed $data
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected static function isResponseDataIndexedArray($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data passed is not an array');
        }

        if (count($data) == 0) {
            return true;
        }

        return count($data) > 0 && is_integer(key($data));
    }

    /**
     * Converts the date to database format
     *
     * @param mixed $inputDate
     * @return string
     */
    public function transformDate($inputDate)
    {
        if (
            null === $inputDate ||
            '00-00-0000' == $inputDate ||
            '0000-00-00' == $inputDate ||
            '' == $inputDate
        ) {
            return '0000-00-00';
        }
        else if ($inputDate instanceof \DateTime) {
            $returnDate = $inputDate->format('Y-m-d');
        }
        else {
            $returnDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));
        }
        return $returnDate;
    }

}