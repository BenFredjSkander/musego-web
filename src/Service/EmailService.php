<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 *
 */
class EmailService
{

    private string $api_key;

    /**
     * @param TransportInterface $mailer
     * @param LoggerInterface $logger
     * @param string $api_key
     */
    public function __construct(private TransportInterface $mailer, private LoggerInterface $logger, string $api_key)
    {
        $this->api_key = $api_key;
    }

    static string $address = "contact.musego@gmail.com";

    /**
     * @param string $recipient single recipient email address
     * @param array $options options to pass to template
     * @param int $templateId template id of the email
     * @return void
     */
    public function sendTemplatedEmail(string $recipient, array $options, int $templateId): void
    {
        $email = (new Email())
            ->from(new Address(self::$address, 'Contact MuseGO')) // celui qui envoie  le mail
            ->to($recipient)
            ->text('hello');

        $email->getHeaders()
            ->addTextHeader('templateId', $templateId)
            ->addParameterizedHeader('params', 'params', $options);

        try {
            $this->logger->info("before sending email");
            $this->mailer->send($email)->toString(); //envoie le mail
            $this->logger->info("after sending email");

        } catch (TransportExceptionInterface $e) {
            $this->logger->alert("error sending email: " . $e->getMessage());
        }
    }


    /**
     * Send email to multiple recipients
     *
     * @param array $recipient
     * @param array $options
     * @param int $templateId
     * @return void
     */
    public function sendTemplatedEmailMultiple(array $recipient, array $options, int $templateId): void
    {
        $email = (new Email())
            ->from(new Address(self::$address, 'Contact MuseGO'))
            ->to(self::$address)
            ->bcc(...$recipient)
            ->text('hello');

        $email->getHeaders()
            ->addTextHeader('templateId', $templateId)
            ->addParameterizedHeader('params', 'params', $options);

        try {
            $this->logger->info("before sending email");
            $this->mailer->send($email);
            $this->logger->info("after sending email");

        } catch (TransportExceptionInterface $e) {
            $this->logger->alert("error sending email: " . $e->getMessage());
        }
    }


    /**
     *
     * Create new campaign and send to selected segment of users
     *
     * @param int $segmentId
     * @param int $templateId
     * @param string $campaignName
     * @param mixed $params
     * @return void
     * @throws GuzzleException
     */
    public function createAndSendEmailCampaign(int $segmentId, int $templateId, string $campaignName, mixed $params): void
    {
        $client = new Client();

        $client->request('POST', 'https://api.sendinblue.com/v3/emailCampaigns', [
            'body' => '{"sender":{"name":"MuseGO","email":"' . self::$address . '"},"recipients":{"segmentIds":[' . $segmentId . ']},"inlineImageActivation":false,"params":' . $params . ',"sendAtBestTime":false,"abTesting":false,"ipWarmupEnable":false,"name":"' . $campaignName . '","templateId":' . $templateId . '}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $this->api_key,
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function sendTextEmail(string $recipient, string $text): void
    {
        $email = (new Email())
            ->from(new Address(self::$address, 'Contact MuseGO')) // celui qui envoie  le mail
            ->to($recipient)
            ->subject('MuseGO')
            ->text($text);

        try {
            $this->logger->info("before sending email");
            $this->mailer->send($email)->toString(); //envoie le mail
            $this->logger->info("after sending email");

        } catch (TransportExceptionInterface $e) {
            $this->logger->alert("error sending email: " . $e->getMessage());
        }
    }
}