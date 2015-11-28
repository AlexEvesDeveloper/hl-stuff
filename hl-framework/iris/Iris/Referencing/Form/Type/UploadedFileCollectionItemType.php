<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UploadedFileCollectionItemType
 *
 * @package Iris\Referencing\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class UploadedFileCollectionItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileItem', 'hidden', array(
                'label' => false,
                'required' => false,
            ))

            // Note: When we have an API to remove files from the backend,
            // we can add this delete button to allow removal of the item
            // from the collection. This can be used in conjunction with
            // the uploader class to perform deletion.
//            ->add('deleteFile', 'submit', array(
//                'label' => false,
//                'attr' => array(
//                    'class' => 'deleteFile'
//                )
//            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'uploaded_file_collection_item';
    }
}
