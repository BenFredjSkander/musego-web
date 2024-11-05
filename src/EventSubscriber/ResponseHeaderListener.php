<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseHeaderListener implements EventSubscriberInterface
{
    private $controller;

    public static function getSubscribedEvents(): array
    {
        return array(
            'kernel.controller' => 'onKernelController',
            'kernel.response' => 'onKernelResponse'
        );
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->controller = $event->getController();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || !is_array($this->controller)) {
            return;
        }

//        if ($this->controller[0] instanceof AdminController) {
        $response = $event->getResponse();

        // Set response headers
        $response->headers->add(array(
            'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache'
        ));
//        }
    }
}