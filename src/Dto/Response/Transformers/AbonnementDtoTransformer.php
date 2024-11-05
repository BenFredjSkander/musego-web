<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\AbonnementDto;
use App\Entity\Abonnement;

class AbonnementDtoTransformer extends AbstractResponseDtoTransformer
{

    /**
     * @param Abonnement $object
     * @return AbonnementDto
     */
    public function transformFromObject($object): AbonnementDto
    {
        $abonnementDto = new AbonnementDto();
        $abonnementDto->id = $object->getId();
        $abonnementDto->idoffre = $object->getIdOffre()->getId();
        $abonnementDto->type = $object->getType();
        $abonnementDto->prix = $object->getPrix();
        $abonnementDto->dateDebut = $object->getDateDebut();
        $abonnementDto->dateFin = $object->getDateFin();
        $abonnementDto->user = $object->getIdUser()->getUsername();
        $abonnementDto->offre = $object->getIdOffre()->getType();
        return $abonnementDto;
    }
}