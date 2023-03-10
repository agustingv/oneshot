<?php

namespace OneShot\Domain\User;
use Symfony\Component\Security\Core\User\UserInterface;

use OneShot\Domain\ValueObjects\EntityId;
use OneShot\Domain\Shared\AbstractDomainModel;
use OneShot\Domain\File\File;


class User extends AbstractDomainModel implements UserInterface
{

    protected array $roles = [];
    protected string $name;
    protected string $mail;
    protected string $password;
    protected string $userIdentifier = '';
    protected string $type = self::class;
    protected ?File $image = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function setRoles(array $roles) : void 
    {
        $this->roles = $roles;
    }

    public function getRoles() : array
    {
        if (empty($this->roles))
        {
            $this->roles[] = 'IS_AUTHENTICATED_FULLY';
        }
        return $this->roles;
    }

    public function eraseCredentials() 
    {

    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function getMail() : string
    {
        return $this->mail;
    }

    public function setMail(string $mail) : void
    {
        $this->mail = $mail;
    }
    
    public function setUserIdentifier(string $userIdentifier) : void
    {
        $this->userIdentifier = $userIdentifier;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getId() : string
    {
        return $this->userIdentifier;
    }

    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    public function getPassword() :  string
    {
        return $this->password;
    }

    public function unsetPassword() : void
    {
        unset($this->password);
    }

    public function getType() : string
    {
        if (!isset($this->type))
        {
            $this->type = self::class;
        }

        return $this->type;
    }

    public function setImage(File $image) : void
    {
        $this->image = $image;
    }

    public function getImage() : File | null
    {
        return $this->image;
    }

    public function fromArray(array $data) : self
    {
        $data = $this->deserialize($data);
        return $this->serializer->denormalize($data, User::class);
    }

}