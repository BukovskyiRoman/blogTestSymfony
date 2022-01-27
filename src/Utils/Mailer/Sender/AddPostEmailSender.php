<?php

namespace App\Utils\Mailer\Sender;

use App\Entity\Post;
use App\Utils\Mailer\DTO\MailerOptions;
use App\Utils\Mailer\MailerSender;

class AddPostEmailSender
{
    private $mailerSender;

    public function __construct(MailerSender $mailerSender)
    {
        $this->mailerSender = $mailerSender;
    }

    public function sendEmail(Post $post)
    {
        $emailOptions = (new MailerOptions())
            ->setRecipient(($post->getUserId()->getEmail()))
            ->setCc('peacerock89@ukr.net')
            ->setSubject('Thank you')
            ->setHtmlTemplate('mailer/add_post_mail.html.twig')
            ->setContext([
                'post' => $post
            ]);

        $this->mailerSender->sendTemplatedEmail($emailOptions);
    }
}