<?php

namespace App\Utils\Mailer;

use App\Utils\Mailer\DTO\MailerOptions;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

/**
 *
 */
class MailerSender
{
    private $mailer;

    private $logger;

    /**
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * @param MailerOptions $options
     * @return void
     */
    public function sendTemplatedEmail(MailerOptions $options)
    {
        $email = (new TemplatedEmail())
            ->to($options->getRecipient())
            ->subject($options->getSubject())
            ->htmlTemplate($options->getHtmlTemplate())
            ->context($options->getContext());

        if ($options->getCc()) {
            $email->cc($options->getCc());
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical($options->getSubject(), [
                'error' => $exception->getTraceAsString()
            ]);
        }
    }
}
