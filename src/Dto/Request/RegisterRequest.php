<?php

namespace App\Dto\Request;

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints as Assert;


class RegisterRequest implements BaseRequestInterface
{
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/^[0-9,*,(,), ,-]*$/", message: "Please provide a valid phone number.")]
    public string $phone;

    #[Assert\NotBlank]
    #[PasswordStrength(['minLength' => 6, 'minStrength' => 2])]
    public string $password;
}
