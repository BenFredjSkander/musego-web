<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                "Veuillez verifier votre compte"
            );
        }
        if ($user->isLocked()) {
            throw new CustomUserMessageAuthenticationException(
                "Votre compte est bloqué veuillez contacter l'administrateur"
            );
        }
    }

    public function checkPostAuth(UserInterface $user)
    {

    }
}