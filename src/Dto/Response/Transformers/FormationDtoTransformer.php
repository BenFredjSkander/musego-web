<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\FormationDto;
use App\Entity\Formation;

class FormationDtoTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param Formation $object
     * @return FormationDto
     */
    public function transformFromObject($object): FormationDto
    {
        $formationDto = new formationDto();
        $formationDto->id = $object->getId();
        $formationDto->nom = $object->getNom();
        $formationDto->dateDebut = $object->getDateDebut();
        $formationDto->dateFin = $object->getDateFin();
        $formationDto->idAtelier = $object->getAtelier()->getId();
        $formationDto->nomAtelier = $object->getAtelier()->getNom();
        $formationDto->idFormateur = $object->getIdFormateur()->getId();
        $formationDto->nomFormateur = $object->getIdFormateur()->getUsername();
        $formationDto->niveau = $object->getNiveau();
        return $formationDto;
    }

}