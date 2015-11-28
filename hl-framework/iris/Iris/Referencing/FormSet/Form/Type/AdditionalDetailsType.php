<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\Form\Type\UploadedFileCollectionItemType;
use Symfony\Component\Form\AbstractType;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Iris\Referencing\Form\Type\FileCollectionItemType;

/**
 * Class AdditionalDetailsType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class AdditionalDetailsType extends AbstractType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachFile', 'submit', array(
                'label' => 'Add Another File'
            ))

            // Note, currently this collection does nothing other than
            // contain a list of uploaded files, but when an API is developed
            // to remove files from IRIS, this field will allow removal of
            // uploaded documents.
            ->add('uploadedFileCollection', 'collection', array(
                'type' => new UploadedFileCollectionItemType(),
                'label' => false,
                'prototype' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                    'label' => false,
                    'required' => false,
                )
            ))
            ->add('uploadFileCollection', 'collection', array(
                'type' => new FileCollectionItemType(),
                'label' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                    'label' => false,
                    'required' => false,
                )
            ))
            ->add('additionalInfo', 'textarea', array(
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'additional_details';
    }
}
