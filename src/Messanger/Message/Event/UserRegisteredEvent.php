<?php

namespace App\Messanger\Message\Event;

use App\Entity\User;

class UserRegisteredEvent
{
    private $user;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __construct(string $user)
    {
        $this->user = $user;
    }
}