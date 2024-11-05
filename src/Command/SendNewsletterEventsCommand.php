<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use App\Service\EmailService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sendnewsletter:events',
    description: 'send scheduled newsletter events emails',
//    aliases: ['app:sdn:events']
)]
class SendNewsletterEventsCommand extends Command
{

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        private EvenementRepository    $evenementRepository,
        private EntityManagerInterface $entityManager,
        private EmailService           $emailService
    )
    {
        parent::__construct();

    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = 'Y-m-d';
        $startDateString = date($format, strtotime('next monday'));
        $startDateObject = DateTime::createFromFormat($format, $startDateString);
        $endDateObject = date_add($startDateObject, date_interval_create_from_date_string("6 days"));
        $endDateString = $endDateObject->format($format);

        $io->info($startDateString);
        $io->info($endDateString);
        $res = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.dateDebut BETWEEN :start AND :end')
            ->setParameter('start', $startDateString)
            ->setParameter('end', $endDateString)
            ->getQuery()
            ->getResult();
        if (count($res) > 0) {
            $dates = array();
            foreach ($res as $evenement) {
                array_push($dates, array('name' => $evenement->getNom(), 'date' => $evenement->getDateDebut()->format($format)));
            }
            $io->info(count($dates));
            $io->info(json_encode(array('CODE' => $dates)));
            $this->emailService->createAndSendEmailCampaign(4, 9, 'EVENTS' . date($format), json_encode(array('CODE' => $dates)));

            $io->success("Campaign sent successfully");
            return Command::SUCCESS;

        } else {
            $io->warning("No events found for next week");
            return Command::FAILURE;
        }
    }
}
