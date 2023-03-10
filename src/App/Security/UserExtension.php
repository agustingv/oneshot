<?php
namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserExtension
{
 
    protected $tokenStorage;


    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function user()
    {
        if ($this->tokenStorage->getToken())
        {
            return $this->tokenStorage->getToken()->getUser();
        }
    }
}