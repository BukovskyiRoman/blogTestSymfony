<?php

namespace App\Utils\Mailer\Sender;

use App\Entity\Post;
use App\Utils\Mailer\DTO\MailerOptions;

class AddPostEmailSender
{
    public function sendEmail(Post $post)
    {
        $emailOptions = (new MailerOptions())
            ->setRecipient(($post->getUserId()->getEmail()))
            ->setCc('peacerock89@ukr.net')
            ->setSubject('Thank you')
            ->setHtmlTemplate('post..')
            ->setContext([

            ]);
        dd($emailOptions);
    }
}