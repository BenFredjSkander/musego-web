<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\OffreDto;
use App\Entity\Offre;

class OffreDtoTransformer extends AbstractResponseDtoTransformer
{

    /**
     * @param Offre $object
     * @return OffreDto
     */
    public function transformFromObject($object): OffreDto
    {
        $offreDto = new OffreDto();
        $offreDto->id = $object->getId();
        $offreDto->type = $object->getType();
        $offreDto->prix = $object->getPrix();
        $offreDto->promotion = $object->getPromotion();
        $offreDto->dateDebut = $object->getDateDebut();
        $offreDto->dateFin = $object->getDateFin();
        $offreDto->description = $object->getDescription();
        $offreDto->image = $object->getImage();
        return $offreDto;
    }
}