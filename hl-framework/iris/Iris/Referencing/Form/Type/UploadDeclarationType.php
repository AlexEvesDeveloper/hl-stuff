<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\UploadableFileConstraintParameters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UploadDeclarationType
 *
 * @package Iris\Referencing\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class UploadDeclarationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('declaration', 'file', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please provide a signed declaration document to upload',
                    )),
                    new Assert\File(array(
                        'maxSize' => UploadableFileConstraintParameters::getMaxUploadFileSize(),
                        'mimeTypes' => UploadableFileConstraintParameters::getAllowedMimeTypes(),
                        'mimeTypesMessage' => UploadableFileConstraintParameters::getInvalidMimeTypeMessage(),
                    )),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'upload_declaration';
    }
}