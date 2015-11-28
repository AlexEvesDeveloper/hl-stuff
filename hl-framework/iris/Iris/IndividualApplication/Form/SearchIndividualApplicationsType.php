<?php

namespace Iris\IndividualApplication\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SearchIndividualApplicationsType
 *
 * @package Iris\IndividualApplication\Form
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class SearchIndividualApplicationsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenceNumber', 'text', array(
                'required' => false,
            ))
            ->add('applicantFirstName', 'text', array(
                'required' => false,
            ))
            ->add('applicantLastName', 'text', array(
                'required' => false,
            ))
            ->add('propertyAddress', 'text', array(
                'required' => false,
            ))
            ->add('propertyTown', 'text', array(
                'required' => false,
            ))
            ->add('propertyPostcode', 'text', array(
                'required' => false,
            ))
            ->add('applicationStatus', 'choice', array(
                'required' => false,
                'choices' => array(
                    'Incomplete' => 'Incomplete',
                    'Complete' => 'Complete',
                ),
                'empty_value' => '- Search All -',
            ))
            ->add('productType', 'choice', array(
                'required' => false,
                'choices' => $this->getProductChoices(),
                'empty_value' => '- Search All -',
            ))
            ->add('resultsPerPage', 'choice', array(
                'required' => false,
                'choices' => array(
                    10 => '10',
                    25 => '25',
                    50 => '50',
                    100 => '100',
                    '' => 'All',
                ),
                'empty_value' => false,
                'data' => 10,
            ))
            ->setMethod('GET')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'data_class' => 'Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'search_individual_applications';
    }

    /**
     * Get array of product choices
     *
     * @return array
     */
    private function getProductChoices()
    {
        $productChoices = array();

        $productManager = new \Manager_Referencing_Product();
        $productResults = $productManager->getAll(true);

        foreach($productResults as $product) {
            $productChoices[$product->name] = $product->name;
        }

        return $productChoices;
    }
}