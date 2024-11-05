<?php

namespace App\Dto\Response;

use DateTimeInterface;

class AbonnementDto
{
    public int $id;

    public int $idoffre;

    public string $type;

    public float $prix;

    public ?DateTimeInterface $dateDebut;

    public ?DateTimeInterface $dateFin;

    public string $user;

    public string $offre;
}