<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Form\Common\Model\PreviousAddress;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AddressHistoryReorderSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param $type
     * @param array $options
     */
    public function __construct($type, array $options = array())
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * PRE_SET_DATA handler
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $sortedData = array();

        if (null === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // First remove all rows
        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        // Reorder input data based on start date
        $timestampSortedData = array();
        foreach ($data as $index => $value) {
            if ($value instanceof PreviousAddress) {
                $startDate = $value->getStartDate();

                if (null !== $startDate) {
                    $timestamp = $value->getStartDate()->getTimestamp();
                }
                else {
                    $timestamp = time();
                }

                if ( ! isset($timestampSortedData[$timestamp])) {
                    $timestampSortedData[$timestamp] = array();
                }

                $timestampSortedData[$timestamp][] = $index;
            }
        }

        krsort($timestampSortedData);
        $counter = 0;

        // Step through reordered data, re-add child form and reorder data array
        foreach ($timestampSortedData as $indexList) {
            foreach ($indexList as $index) {
                $form->add($counter, $this->type, array_replace(array(
                    'property_path' => '[' . $counter . ']',
                ), $this->options));

                $sortedData[$counter] = $data[$index];
                $counter++;
            }
        }

        $event->setData($sortedData);
    }

    /**
     * PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $sortedData = array();

        if (null === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // First remove all rows
        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        // Reorder input data based on start date
        $timestampSortedData = array();
        foreach ($data as $index => $value) {
            if (is_array($value)) {
                $timestamp = null;

                if (isset($value['startDate']) &&
                    isset($value['startDate']['year']) &&
                    isset($value['startDate']['month']) &&
                    isset($value['startDate']['day'])) {
                    $timestamp = DateTime::createFromFormat(
                        'Y-n-j',
                        $value['startDate']['year'] . '-' . $value['startDate']['month'] . '-' . $value['startDate']['day']
                    );

                    if ($timestamp !== false) {
                        $timestamp = $timestamp->getTimestamp();
                    }
                }

                if (null === $timestamp) {
                    // Default to current time if invalid
                    $timestamp = time();
                }

                if ( ! isset($timestampSortedData[$timestamp])) {
                    $timestampSortedData[$timestamp] = array();
                }

                $timestampSortedData[$timestamp][] = $index;
            }
        }

        krsort($timestampSortedData);
        $counter = 0;

        // Step through reordered data, re-add child form and reorder data array
        foreach ($timestampSortedData as $indexList) {
            foreach ($indexList as $index) {
                $form->add($counter, $this->type, array_replace(array(
                    'property_path' => '[' . $counter . ']',
                ), $this->options));

                $sortedData[$counter] = $data[$index];
                $counter++;
            }
        }

        $event->setData($sortedData);
    }
}
