<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class AddPostEvent extends Event
{
    private $post;

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

}