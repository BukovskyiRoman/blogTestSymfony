<?php

namespace App\EventSubscriber;

use App\Event\AddPostEvent;
use App\Utils\Mailer\Sender\AddPostEmailSender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddPostSubscriber implements EventSubscriberInterface
{
    private $postEmailSender;

    public function __construct(AddPostEmailSender $postEmailSender)
    {
        $this->postEmailSender = $postEmailSender;
    }
    public function onAddPostEvent(AddPostEvent $event)
    {
       $post = $event->getPost();
        $this->postEmailSender->sendEmail($post);
    }

    public static function getSubscribedEvents()
    {
        return [
            AddPostEvent::class => 'onAddPostEvent',
        ];
    }
}
