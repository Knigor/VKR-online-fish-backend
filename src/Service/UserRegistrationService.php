<?php

// src/Service/UserRegistrationService.php
namespace App\Service;

use App\Dto\RegisterUserDto;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function registerUser(RegisterUserDto $registerDto): Users
    {
        $user = new Users();
        $user
            ->setNameUser($registerDto->nameUser)
            ->setEmail($registerDto->email)
            ->setPhone($registerDto->phone)
            ->setPasswordHash($this->passwordHasher->hashPassword($user, $registerDto->password))
            ->setRole('ROLE_USER');

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}