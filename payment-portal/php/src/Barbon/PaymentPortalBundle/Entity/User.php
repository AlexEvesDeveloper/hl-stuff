<?php

namespace Barbon\PaymentPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @package Barbon\PaymentPortalBundle\Entity
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=40, unique=true)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_portal_css", type="text", nullable=true)
     */
    private $paymentPortalCss;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get paymentPortalCss
     *
     * @return string
     */
    public function getPaymentPortalCss()
    {
        return $this->paymentPortalCss;
    }

    /**
     * Set paymentPortalCss
     *
     * @param string $paymentPortalCss
     * @return $this
     */
    public function setPaymentPortalCss($paymentPortalCss)
    {
        $this->paymentPortalCss = $paymentPortalCss;
        return $this;
    }

    /**
     * Get ID digest
     *
     * @return string
     */
    public function getIdDigest()
    {
        return sha1($this->id . md5($this->apiKey));
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return array(
            'ROLE_USER',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
