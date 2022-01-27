<?php

namespace App\Messanger\Message\Event;

use App\Entity\User;

class UserRegisteredEvent
{
    private $userId;

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }
}