<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Update user last_active_at attribute and skip for api doc users
 */
class ActivitySubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(
        EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function onTerminate(): void
    {
        $user = $this->security->getUser();
        if ($this->security->isGranted('ROLE_API')) {
            return;
        }
        if (($user instanceof UserInterface) && !($user->isActiveNow())) {
            $user->setLastActiveAt(new \DateTime());
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::TERMINATE => [['onTerminate', 20]],
        ];
    }


}