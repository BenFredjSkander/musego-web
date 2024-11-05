<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class EmailVerifRequest implements BaseRequestInterface
{
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    public string $code;
}