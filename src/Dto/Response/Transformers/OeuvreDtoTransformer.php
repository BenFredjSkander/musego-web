<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\OeuvreDto;
use App\Entity\Oeuvre;

class OeuvreDtoTransformer extends AbstractResponseDtoTransformer
{
    public function __construct(private ArtisteDtoTransformer $artisteTransformer, private CategorieDtoTransformer $categorieTransformer)
    {

    }


    /**
     * @param Oeuvre $object
     * @return OeuvreDto
     */

    public function transformFromObject($object): OeuvreDto
    {
        $oeuvreDto = new OeuvreDto();

        $oeuvreDto->id = $object->getId();
        $oeuvreDto->titre = $object->getTitre();
        $oeuvreDto->lieuConservation = $object->getLieuConservation();
        $oeuvreDto->description = $object->getDescription();
        $oeuvreDto->image = $object->getImage();
        $oeuvreDto->dateCreation = $object->getDateCreation();
        // Mapping de la clé étrangère
        $oeuvreDto->idCategorie = $object->getIdCategorie()->getNom();
        $oeuvreDto->artiste = $this->artisteTransformer->transformFromObject($object->getIdArtiste());
        $oeuvreDto->categorie = $this->categorieTransformer->transformFromObject($object->getIdCategorie());
        return $oeuvreDto;

    }


}