<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension;

use Barbon\HostedApi\AppBundle\Form\Common\Extension\Core\ChoiceList\LabelAwareChoiceListInterface;
use Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension\Exception\LookupIdNotFoundException;
use Twig_Extension;

abstract class AbstractIrisLookupServiceExtension extends Twig_Extension
{
    /**
     * @var LabelAwareChoiceListInterface
     */
    protected $choiceList;

    /**
     * Constructor
     *
     * @param LabelAwareChoiceListInterface $choiceList
     */
    public function __construct(LabelAwareChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * Lookup the id to the label name
     *
     * @param $id
     * @return mixed
     * @throws LookupIdNotFoundException
     */
    public function lookupLabel($id)
    {
        $values = $this->choiceList->getLabelsForValues(array((string) $id));

        if (1 == count($values)) {
            return $values[0];
        }

        // Name not found, somethings gone wrong
        throw new LookupIdNotFoundException(sprintf('Label for lookup id %s could not be found', $id));
    }
}