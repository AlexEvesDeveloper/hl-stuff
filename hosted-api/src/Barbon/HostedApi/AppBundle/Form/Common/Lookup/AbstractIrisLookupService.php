<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup;

use Barbon\HostedApi\AppBundle\Form\Common\Extension\Core\ChoiceList\LabelAwareChoiceListInterface;
use Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

abstract class AbstractIrisLookupService implements LabelAwareChoiceListInterface
{
    /**
     * @var IrisEntityManager
     */
    protected $lookupContainer;

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
    private $labels = array();

    /**
     * @var bool
     */
    private $isInitialised = false;

    /**
     * Constructor
     *
     * @param IrisLookupContainer $lookupContainer
     */
    public function __construct(IrisLookupContainer $lookupContainer)
    {
        $this->lookupContainer = $lookupContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $this->initialiseChoices();
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
        $this->initialiseChoices();
        $products = array();
        foreach ($this->values as $i => $choice) {
            $products[$this->values[$i]] = $this->choices[$i];
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        $this->initialiseChoices();
        $products = array();
        foreach ($this->labels as $i => $choice) {
            $products[$this->choices[$i]] = $this->labels[$i];
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
        $this->initialiseChoices();
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
        $this->initialiseChoices();
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
        $this->initialiseChoices();
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

    public function getLabelsForValues(array $values)
    {
        $this->initialiseChoices();
        $labels = array();

        foreach ($values as $i => $givenValue) {
            foreach ($this->values as $j => $value) {
                if ($value === $givenValue) {
                    $labels[$i] = $this->labels[$j];
                    unset($values[$i]);

                    if (0 === count($values)) {
                        break 2;
                    }
                }
            }
        }

        return $labels;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForChoices(array $choices)
    {
        $this->initialiseChoices();
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
        $this->initialiseChoices();
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

    /**
     * Build choice array based on array of LookupEntry instances
     *
     * @param LookupEntry[] $lookupEntries
     * @return array
     */
    protected function buildChoices(array $lookupEntries)
    {
        if ( ! $this->isInitialised) {
            foreach ($lookupEntries as $lookupEntry) {
                $this->values[] = (string) $lookupEntry->getIndex();
                $this->choices[] = (string) $lookupEntry->getIndex();
                $this->labels[] = (string) $lookupEntry->getValue();
            }

            $this->isInitialised = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function initialiseChoices();
}
