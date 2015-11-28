<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class FileCollectionItemType extends AbstractType
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

// todo: consider a better way to handle constraints for files
//                'constraints' => array(
//                    new Constraints\File(array(
//                        'maxSize' => UploadableFileConstraintParameters::getMaxUploadFileSize(),
//                        'mimeTypes' => UploadableFileConstraintParameters::getAllowedMimeTypes(),
//                        'mimeTypesMessage' => UploadableFileConstraintParameters::getInvalidMimeTypeMessage(),
//                    )),
//                ),

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
