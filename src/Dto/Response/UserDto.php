<?php

namespace App\Dto\Response;

use DateTimeInterface;

class UserDto
{
    public int $id;

    public string $email;

    public string $username;

    public bool $locked;

    public ?DateTimeInterface $lastActiveAt;

    public ?DateTimeInterface $createdDate;

    public string $phone;

}
