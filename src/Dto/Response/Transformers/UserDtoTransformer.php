<?php

namespace App\Dto\Response\Transformers;

use App\Dto\Response\UserDto;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;


class UserDtoTransformer extends AbstractResponseDtoTransformer
{

    /**
     * @param User|UserInterface $object
     * @return UserDto
     */
    public function transformFromObject($object): UserDto
    {
        $userDto = new UserDto();
        $userDto->id = $object->getId();
        $userDto->email = $object->getEmail();
        $userDto->username = $object->getUsername();
        $userDto->lastActiveAt = $object->getLastActiveAt();
        $userDto->locked = $object->isLocked();
        $userDto->createdDate = $object->getCreatedDate();
        $userDto->phone = $object->getPhoneNumber();
        return $userDto;
    }
}
