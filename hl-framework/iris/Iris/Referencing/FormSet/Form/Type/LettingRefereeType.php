<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\Form\Type\StepTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Iris\Referencing\Form\Type\LettingRefereeType as LettingRefereeModelType;

/**
 * Class LettingRefereeType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LettingRefereeType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lettingReferee', new LettingRefereeModelType())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
                'is_agent_context' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // todo: If this is an agent and not a landlord, currently always agent
        if ($options['is_agent_context']) {

            /** @var \Barbondev\IRISSDK\Agent\Agent\Model\Agent $agent */
            $agent = \Zend_Registry::get('iris_container')
                ->get('iris_sdk_client_registry.agent_context')
                ->getAgentClient()
                ->getAgent()
            ;

            if ($agent && $agent->getAgentBrand()) {

                $view->vars['agent'] = array(
                    'name' => $agent->getAgentBrand()->getBrandName(),
                    'type' => 1, // Always letting agent
                    'flat' => $agent->getAgentBrand()->getAddress()->getFlat(),
                    'houseName' => $agent->getAgentBrand()->getAddress()->getHouseName(),
                    'houseNumber' => $agent->getAgentBrand()->getAddress()->getHouseNumber(),
                    'street' => $agent->getAgentBrand()->getAddress()->getStreet(),
                    'town' => $agent->getAgentBrand()->getAddress()->getTown(),
                    'postcode' => $agent->getAgentBrand()->getAddress()->getPostcode(),
                    'dayPhone' => $agent->getAgentBrand()->getPhone(),
                    'fax' => $agent->getFax(),
                    'email' => $agent->getEmail(),
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'letting_referee';
    }
}
