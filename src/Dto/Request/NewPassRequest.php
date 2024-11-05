<?php

namespace App\Dto\Request;

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints as Assert;

class NewPassRequest implements BaseRequestInterface
{

    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    #[PasswordStrength(['minLength' => 6, 'minStrength' => 2])]
    public string $newpass;
}