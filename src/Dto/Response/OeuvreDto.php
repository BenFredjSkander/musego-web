<?php

namespace App\Dto\Response;

use DateTimeInterface;

class OeuvreDto
{
    public int $id;

    public string $titre;
    public string $image;
    public string $lieuConservation;

    public string $description;
    public ?DateTimeInterface $dateCreation;

    public ArtisteDto $artiste;
    public CategorieDto $categorie;
}