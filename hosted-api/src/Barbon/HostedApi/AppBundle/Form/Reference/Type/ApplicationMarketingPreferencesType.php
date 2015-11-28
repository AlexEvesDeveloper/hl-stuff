<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\AbstractReferencingApplication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Barbon\HostedApi\AppBundle\Form\Common\DataTransformer\InverseBooleanTransformer;

/**
 * Application Marketing Preferences Form
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ApplicationMarketingPreferencesType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $inverseBooleanTransformer = new InverseBooleanTransformer();

        $builder
            ->add(
                // Inverse boolean transformer, to turn a true value into a false value
                // - this field is check to opt out (false)
                $builder->create('canContactApplicantByPhoneAndPost', 'checkbox', array(
                    'required' => false,
                    'data' => true,
                ))->addModelTransformer($inverseBooleanTransformer)
            )
            ->add('canContactApplicantBySMSAndEmail', 'checkbox', array(
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
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Reference\Model\AbstractReferencingApplication',
            'user_type' => $this->options['user_type']
        ));
    }

    /**
     * Send first and last name of each Application to the view
     *
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $application = $form->getData();
        
        if ($application instanceof AbstractReferencingApplication) {
            $view->vars['applicant_name'] = sprintf(
                '%s %s %s',
                $application->getTitle(),
                $application->getFirstName(),
                $application->getLastName()
            );
        }

        // Enable the view to render the landlord or agent specific text.
        // todo: explore a cleaner solution for sending this variable to the template
        $view->vars['user_type'] = $options['user_type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'application_marketing_preferences';
    }
}

