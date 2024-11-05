<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\ArtisteDto;
use App\Entity\Artiste;

class ArtisteDtoTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param Artiste $object
     * @return ArtisteDto
     */
    public function transformFromObject($object): ArtisteDto
    {
        $artisteDto = new ArtisteDto();
        $artisteDto->id = $object->getId();
        $artisteDto->nom = $object->getNom();
        $artisteDto->prenom = $object->getPrenom();
        $artisteDto->cin = $object->getCin();
        $artisteDto->image = $object->getImage();
        $artisteDto->nationalite = $object->getNationalite();
        $artisteDto->specialite = $object->getSpecialite();
        $artisteDto->email = $object->getEmail();
        $artisteDto->adresse = $object->getAdresse();
        $artisteDto->dateNaissance = $object->getDateNaissance();
        return $artisteDto;
    }
}