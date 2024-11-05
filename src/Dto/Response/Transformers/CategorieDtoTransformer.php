<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\CategorieDto;
use App\Entity\Categorie;

class CategorieDtoTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param Categorie $object
     * @return CategorieDto
     */
    public function transformFromObject($object): CategorieDto
    {
        $categorieDto = new CategorieDto();
        $categorieDto->id = $object->getId();
        $categorieDto->nom = $object->getNom();
        $categorieDto->description = $object->getDescription();
        $categorieDto->image = $object->getImage();
        return $categorieDto;
    }


}