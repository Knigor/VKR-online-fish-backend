<?php

// src/Dto/RegisterUserDto.php
namespace App\Dto;

use App\Entity\Users;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $nameUser;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 255)]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 30)]
    public string $phone;
}