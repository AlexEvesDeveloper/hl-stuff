<?php

namespace Iris\Referencing\Form\Type;

use Barbondev\IRISSDK\Common\Enumeration\ReferencingApplicationTypeOptions;
use Iris\Common\Form\DataTransformer\YearMonthDurationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PreviousAddressType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class PreviousAddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Get the current application
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $currentApplication */
        $currentApplication = \Zend_Registry::get('iris_container')
            ->get('iris.referencing.application.current_form_flow_records')
            ->getApplication()
        ;

        // Disallow abroad address
        $isForeignConstraints = array();
        if ($currentApplication->getApplicationType() == ReferencingApplicationTypeOptions::GUARANTOR) {

            // If self employed
            if (2 == $currentApplication->getEmploymentStatus()) {
                $isForeignConstraints = array(
                    new Assert\False(array(
                        'message' => 'Address cannot be outside the UK if the applicant is self employed',
                    )),
                );
            }
        }

        // Instantiate a data transformer to handle compound years+months <=> months field
        $yearMonthDurationTransformer = new YearMonthDurationTransformer();

        $builder
            ->add('isForeign', 'checkbox', array(
                'label' => 'If the address is outside of the UK please tick the box',
                'required' => false,
                'constraints' => $isForeignConstraints,
            ))
            ->add('address', new AddressType(), array(
                'read_only_except_postcode' => $options['read_only_except_postcode'],
            ))
            // Duration of stay, in years and months (transformed to months)
            ->add(
                $builder
                    ->create('durationMonths', new YearMonthDurationType(), array(
                        'label' => 'Period at Address',
                        'attr' => array(
                            'class' => 'address-history-duration'
                        ),
                        'constraints' => array(
                            new Assert\GreaterThan(array(
                                'value' => 0,
                                'message' => 'Please provide a period at address for all addresses',
                            )),
                        ),
                    ))
                    ->addModelTransformer($yearMonthDurationTransformer)
            )
            ->add('addressHistoryUuId', 'hidden', array(
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\AddressHistory',
            'read_only_except_postcode' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'previous_address';
    }
}