<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\Form\Type\StepTypeInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractFormStepType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractFormStepType extends AbstractType implements StepTypeInterface
{
    /**
     * Get agent progressive store
     *
     * @return \Iris\Referencing\FormSet\ProgressiveStore\AgentProgressiveStore
     */
    public function getAgentProgressiveStore()
    {
        return \Zend_Registry::get('iris_container')
            ->get('iris.referencing.form_set.progressive_store.agent_progressive_store')
        ;
    }

    /**
     * Get landlord progressive store
     *
     * @return \Iris\Referencing\FormSet\ProgressiveStore\LandlordProgressiveStore
     */
    public function getLandlordProgressiveStore()
    {
        return \Zend_Registry::get('iris_container')
            ->get('iris.referencing.form_set.progressive_store.landlord_progressive_store')
        ;
    }

    /**
     * Unsets all choices from array except for the choice keys passed to $choiceKeysToStay
     *
     * @param array $choices
     * @param array $choiceKeysToStay
     * @return array
     */
    protected function unsetAllChoicesByKeyExcept(array $choices, array $choiceKeysToStay)
    {
        foreach ($choices as $key => $choice) {
            if (!in_array($key, $choiceKeysToStay)) {
                unset($choices[$key]);
            }
        }
        return $choices;
    }
}