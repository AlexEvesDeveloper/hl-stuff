<?php

namespace Barbon\PaymentPortalBundle\Command;

use Barbon\PaymentPortalBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UserCreateCommand
 *
 * @package Barbon\PaymentPortalBundle\Command
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class UserCreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('barbon-payment-portal:user:create')
            ->setDescription('Create a new payment portal API user')
            ->addArgument('name', InputArgument::REQUIRED, 'Human-readable name of the API user')
            ->addArgument('apiKey', InputArgument::OPTIONAL, '40 character (max) api key (optional). If not provided, one will be generated')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $serializer = $this->getContainer()->get('serializer');

        $user = new User();
        $user
            ->setName($input->getArgument('name'))
            ->setApiKey($input->getArgument('apiKey'))
        ;

        $em->persist($user);
        $em->flush();

        $output->writeln($serializer->serialize($user, 'json'));
    }
}