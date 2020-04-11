<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * User constructor.
     * @param $roles
     * @param $username
     * @param $password
     */
    public function __construct($roles, $username, $password)
    {
        $this->roles = $roles;
        $this->username = $username;
        $this->password = $password;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array The user roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string|null The salt
     *
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function eraseCredentials()
    {

    }
}
