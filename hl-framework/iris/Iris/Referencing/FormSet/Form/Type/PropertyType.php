<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Iris\Referencing\Form\Type\AddressType;
use Iris\Utility\Lookup\Lookup;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Iris\Referencing\Form\Type\MoneyWithoutStringTransformerType;

/**
 * Class PropertyType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class PropertyType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Minimum tenancy start date for back-end validation
        $minTenancyStartDate = new \DateTime();
        $minTenancyStartDate->sub(new \DateInterval('P1D'));

        // Maximum tenancy start date for back-end validation
        // todo: Properly parameterise how far ahead tenancy start dates can be.
        $maxTenancyStartDays = 200;
        $maxTenancyStartDate = new \DateTime();
        $maxTenancyStartDate->add(new \DateInterval('P' . $maxTenancyStartDays . 'D'));

        // Minimum and maximum tenancy start years for front end
        $minTenancyStartDateYear = $minTenancyStartDate->format('Y');
        $maxTenancyStartDateYear = $maxTenancyStartDate->format('Y');
        $tenancyStartYears = array();

        // Add earliest year
        $tenancyStartYears[$minTenancyStartDateYear] = $minTenancyStartDateYear;

        // If latest year is different to earliest, add it too
        if ($minTenancyStartDateYear != $maxTenancyStartDateYear) {
            $tenancyStartYears[$maxTenancyStartDateYear] = $maxTenancyStartDateYear;
        }

        $builder
            ->add('address', new AddressType())
            ->add('totalRent', new MoneyWithoutStringTransformerType(), array(
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\GreaterThan(array('value' => 0)),
                    new Assert\NotBlank(),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                ),
                'label' => 'Total Rent (£ Per Calendar Month)',
            ))
            ->add('rentGuaranteeOfferingType', 'choice', array(
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::RENT_GUARANTEE_OFFERING_TYPE),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'How is Rent Guarantee offered to your landlord?',
            ))
            ->add('propertyLetType', 'choice', array(
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::PROPERTY_LET_TYPE),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'Property Let Type',
            ))
            ->add('numberOfBedrooms', 'integer', array(
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array('value' => 0)),
                ),
                'label' => 'How many bedrooms does the property have?',
                'attr' => array(
                    'min' => 0,
                ),
                'empty_data' => '0',
            ))
            ->add('propertyType', 'choice', array(
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::PROPERTY_TYPE),
                'empty_value' => '- Please Select -',
                'label' => 'Property Type',
                'empty_data' => '6'
            ))
            ->add('propertyBuiltInRangeType', 'choice', array(
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::PROPERTY_BUILT_IN_RANGE_TYPE),
                'empty_value' => '- Please Select -',
                'label' => 'When was the property built?',
                'empty_data' => '10',
            ))
            ->add('tenancyTermInMonths', 'choice', array(
                'choices' => array(
                    '6' => '6',
                    '12' => '12',
                    '18' => '18',
                    '24' => '24',
                ),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'Tenancy Term in Months',
            ))
            ->add('numberOfTenants', 'choice', array(
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'Number of Tenants',
            ))
            ->add('tenancyStartDate', 'date', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(array(
                        'value' => $minTenancyStartDate,
                        'message' => 'The tenancy start date cannot be in the past.',
                    )),
                    new Assert\LessThanOrEqual(array(
                        'value' => $maxTenancyStartDate,
                        'message' => sprintf('The tenancy start date cannot be more than %d days in the future.',
                            $maxTenancyStartDays),
                    )),
                ),
//                'empty_value' => array(
//                    'year' => 'Year',
//                    'month' => 'Month',
//                    'day' => 'Day',
//                ),
//                'years' => $tenancyStartYears,
                'label' => 'Tenancy Start Date (dd/mm/yyyy)',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-provide' => 'datepicker',
                ),
            ))
        ;

        // As this is the first step, remove the back button.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->getParent()->remove('back');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'property';
    }
}
