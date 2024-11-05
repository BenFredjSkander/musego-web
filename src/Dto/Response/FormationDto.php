<?php

namespace App\Dto\Response;

use App\Dto\Response\Transformers\AbstractResponseDtoTransformer;
use App\Entity\Formation;
use DateTimeInterface;

class FormationDto
{
    public int $id;

    public string $nom;

    public ?DateTimeInterface $dateDebut;

    public ?DateTimeInterface $dateFin;

    public string $niveau;
    public int $idAtelier;
    public string $nomAtelier;

    public int $idFormateur;
    public string $nomFormateur;

}