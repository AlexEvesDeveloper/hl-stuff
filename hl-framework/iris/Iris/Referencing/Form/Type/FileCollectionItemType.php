<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\UploadableFileConstraintParameters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FileCollectionItemType
 *
 * @package Iris\Referencing\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class FileCollectionItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileItem', 'file', array(
                'label' => false,
                'required' => false,
                'constraints' => array(
                    new Assert\File(array(
                        'maxSize' => UploadableFileConstraintParameters::getMaxUploadFileSize(),
                        'mimeTypes' => UploadableFileConstraintParameters::getAllowedMimeTypes(),
                        'mimeTypesMessage' => UploadableFileConstraintParameters::getInvalidMimeTypeMessage(),
                    )),
                ),
            ))
            ->add('deleteFile', 'submit', array(
                'label' => false,
                'attr' => array(
                    'class' => 'deleteFile'
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'file_collection_item';
    }
}
