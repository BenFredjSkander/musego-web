<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\EvenementDto;
use App\Dto\Response\UserDto;
use App\Entity\Evenement;
use App\Entity\User;


class EvenementDtoTransformer extends AbstractResponseDtoTransformer
{

    /**
     * @param Evenement $object
     * @return EvenementDto
     */
    public function transformFromObject($object): EvenementDto
    {
        $evenementDto = new EvenementDto();
        $evenementDto->id = $object->getId();
        $evenementDto->nom = $object->getNom();
        $evenementDto->type = $object->getType();
        $evenementDto->lieu = $object->getLieu();
        $evenementDto->date_debut = $object->getDateDebut();
        $evenementDto->date_fin = $object->getDateFin();
        $evenementDto->description = $object->getDescription();
        $evenementDto->poster = $object->getPoster();
        $evenementDto->nbParticipants = $object->getNbParticipants();

        return $evenementDto;
    }
}
