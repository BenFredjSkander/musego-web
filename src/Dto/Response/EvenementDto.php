<?php

namespace App\Dto\Response;

use DateTimeInterface;

class EvenementDto
{
    public int $id;

    public string $nom;

    public string $type;


    public string $lieu;


    public DateTimeInterface $date_debut;

    public DateTimeInterface $date_fin;

    public string $description;


    public string $poster;

    public int $nbParticipants;

    public bool $participated = false;


}
