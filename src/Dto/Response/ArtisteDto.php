<?php

namespace App\Dto\Response;

use DateTimeInterface;

class ArtisteDto
{
    public int $id;

    public string $nom;
    public string $prenom;

    public string $image;
    public string $adresse;
    public string $email;
    public string $nationalite;
    public string $specialite;
    public int $cin;
    public ?DateTimeInterface $dateNaissance;
}