<?php

namespace App\EventSubscriber;

use App\Repository\FormationRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private FormationRepository $formationRepository;
    private UrlGeneratorInterface $router;

    public function __construct(
        FormationRepository   $formationRepository,
        UrlGeneratorInterface $router
    )
    {
        $this->formationRepository = $formationRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        $formations = $this->formationRepository->formationsForCalendar($start, $end);
        foreach ($formations as $formation) {
            // this create the events with your data (here booking data) to fill calendar
            $formationEvent = new Event(
                $formation->getNom(),
                $formation->getDateDebut(),
                $formation->getDateFin() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

//            $formationEvent->setOptions([
//                'backgroundColor' => 'red',
//                'borderColor' => 'red',
//            ]);
            $formationEvent->addOption(
                'url',
                $this->router->generate('app_back_formation_show_formation', ['id' => $formation->getId()])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($formationEvent);
        }
    }
}