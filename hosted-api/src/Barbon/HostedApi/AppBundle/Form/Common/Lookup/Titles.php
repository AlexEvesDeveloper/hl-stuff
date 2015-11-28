<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

final class Titles implements ChoiceListInterface
{
    /**
     * @var string[]
     */
    private $choices = array();

    /**
     * @var string[]
     */
    private $values = array();

    /**
     * @var string[]
     */
    private $labels = array(
        'Mr',
        'Mrs',
        'Ms',
        'Miss',
        'Dr',
        'Rev',
        'Sir',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = $this->labels;
        $this->choices = $this->labels;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $products = array();
        foreach ($this->choices as $i => $choice) {
            $products[$this->choices[$i]] = $this->values[$i];
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $products = array();
        foreach ($this->values as $i => $choice) {
            $products[$this->values[$i]] = $this->choices[$i];
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreferredViews()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainingViews()
    {
        $views = array();
        foreach ($this->labels as $i => $label) {
            $views[] = new ChoiceView($this->values[$i], $this->values[$i], $label);
        }

        return $views;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesForValues(array $values)
    {
        $choices = array();

        foreach ($values as $i => $givenValue) {
            foreach ($this->values as $j => $value) {
                if ($value === $givenValue) {
                    $choices[$i] = $this->choices[$j];
                    unset($values[$i]);

                    if (0 === count($values)) {
                        break 2;
                    }
                }
            }
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesForChoices(array $choices)
    {
        $values = array();

        foreach ($choices as $i => $givenChoice) {
            foreach ($this->choices as $j => $choice) {
                if ($choice === $givenChoice) {
                    $values[$i] = $this->values[$j];
                    unset($choices[$i]);

                    if (0 === count($choices)) {
                        break 2;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForChoices(array $choices)
    {
        $indices = array();

        foreach ($choices as $i => $givenChoice) {
            foreach($this->choices as $j => $choice) {
                if ($choice === $givenChoice) {
                    $indices[$i] = $j;
                    unset($choices[$i]);

                    if (0 === count($choices)) {
                        break 2;
                    }
                }
            }
        }

        return $indices;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForValues(array $values)
    {
        $indices = array();

        foreach ($values as $i => $givenValue) {
            foreach ($this->values as $j => $value) {
                if ($value === $givenValue) {
                    $indices[$i] = $j;
                    unset($values[$i]);

                    if (0 === count($values)) {
                        break 2;
                    }
                }
            }
        }

        return $indices;
    }
}
