<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\AtelierDto;
use App\Entity\Atelier;

class AtelierDtoTransformer extends AbstractResponseDtoTransformer
{


    /**
     * @param Atelier $object
     * @return AtelierDto
     */
    public function transformFromObject($object): AtelierDto
    {
        $atelierDto = new atelierDto();
        $atelierDto->id = $object->getId();
        $atelierDto->nom = $object->getNom();
        $atelierDto->image = $object->getImage();
        $atelierDto->createdAt = $object->getCreatedAt();
        return $atelierDto;
    }
}