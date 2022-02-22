<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 *
 */
class LikeController extends AbstractController
{
    #[Route('/like/post', name: 'like_post', methods: 'POST')]
    public function likePost(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, CacheInterface $cache)
    {
        $data = json_decode($request->getContent());
        $likeCheck = $entityManager->getRepository(Like::class)
            ->findOneBy(['post' => $data->post_id, 'user' => $data->user_id]);

        if ($likeCheck) {
            $entityManager->remove($likeCheck);
            $entityManager->flush();
            return new Response(Response::HTTP_OK);
        }

        $post = $entityManager->getRepository(Post::class)->findOneBy(['id' => $data->post_id]);
        $user = $entityManager->getRepository(User::class)->find($data->user_id);

        $likePost = new Like();
        $likePost->setUser($user);
        $likePost->setPost($post);

        $entityManager->persist($likePost);
        $entityManager->flush();
        return new Response(Response::HTTP_OK);
    }
}
