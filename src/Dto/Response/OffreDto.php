<?php

namespace App\Dto\Response;

use DateTimeInterface;

class OffreDto
{
    public int $id;

    public string $type;

    public ?string $image;

    public ?string $description;

    public float $prix;

    public float $promotion;

    public ?DateTimeInterface $dateDebut;

    public ?DateTimeInterface $dateFin;

}