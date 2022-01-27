<?php

namespace App\Messanger\Handler\Event;

use App\Entity\User;
use App\Messanger\Message\Event\UserRegisteredEvent;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Couchbase\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 *
 */
class UserRegisteredHandler implements MessageHandlerInterface
{
    private $emailVerifier;

    private $doctrine;

    public function __contruct(EmailVerifier $emailVerifier, ManagerRegistry $doctrine)
    {
        $this->emailVerifier = $emailVerifier;
        $this->doctrine = $doctrine;
    }

    public function __invoke(UserRegisteredEvent $event)
    {
       $userId = $event->getUserId();

        $entityManager = $this->doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        dd($user);
//        $emailSignature = $this->emailVerifier->generateEmailSignature('app_verify_email', $user);
    }
}