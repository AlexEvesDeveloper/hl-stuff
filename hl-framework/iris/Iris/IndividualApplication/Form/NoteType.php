<?php

namespace Iris\IndividualApplication\Form;

use Iris\Common\UploadableFileConstraintParameters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class NoteType
 *
 * @package Iris\IndividualApplication\Form
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class NoteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', 'textarea', array(
                'label' => 'Message',
            ))
            ->add('attachment', 'file', array(
                'constraints' => array(
                    new Assert\File(array(
                        'maxSize' => UploadableFileConstraintParameters::getMaxUploadFileSize(),
                        'mimeTypes' => UploadableFileConstraintParameters::getAllowedMimeTypes(),
                        'mimeTypesMessage' => UploadableFileConstraintParameters::getInvalidMimeTypeMessage(),
                    )),
                ),
            ))
            ->add('add', 'submit', array(
                'label' => 'Send Message',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'note';
    }
}