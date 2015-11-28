<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Extension\Core\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

interface LabelAwareChoiceListInterface extends ChoiceListInterface
{
    /**
     * Returns the list of labels.
     *
     * @return array The labels with their choices as keys
     */
    public function getLabels();

    /**
     * Returns the labels corresponding to the given values.
     *
     * The labels can have any data type.
     *
     * The labels must be returned with the same keys and in the same order
     * as the corresponding values in the given array.
     *
     * @param array $values An array of choice values. Not existing values in
     *                      this array are ignored
     *
     * @return array An array of labels with ascending, 0-based numeric keys
     */
    public function getLabelsForValues(array $values);
}