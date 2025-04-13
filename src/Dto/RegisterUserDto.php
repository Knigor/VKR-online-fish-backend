<?php

// src/Dto/RegisterUserDto.php
namespace App\Dto;

use App\Entity\Users;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto
{

    public string $nameUser;

    public string $email;


    public string $password;

    public string $phone;
}