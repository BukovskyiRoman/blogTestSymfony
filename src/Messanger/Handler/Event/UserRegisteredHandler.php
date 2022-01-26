<?php

namespace App\Messanger\Handler\Event;

use App\Messanger\Message\Event\UserRegisteredEvent;
use App\Security\EmailVerifier;
use Couchbase\UserManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 *
 */
class UserRegisteredHandler implements MessageHandlerInterface
{
    private $emailVerifier;

    private $userManager;

    public function __contruct(EmailVerifier $emailVerifier, UserManager $userManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->userManager = $userManager;
    }

    public function __invoke(UserRegisteredEvent $event)
    {
//        $userId = $event->getUser();
//
//        $user = $this->userManager->find($userId);
//
//        if (!$user) {
//            return;
//        }
//
//        $emailSignature = $this->emailVerifier->generateEmailSignature('app_verify_email', $user);
    }
}