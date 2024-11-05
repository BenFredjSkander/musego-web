<?php

namespace App\Dto\Response;

use DateTimeInterface;

class AtelierDto
{
    public int $id;

    public string $nom;

    public string $image;

    public ?DateTimeInterface $createdAt;

}