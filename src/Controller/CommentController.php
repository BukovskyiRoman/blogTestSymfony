<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/new', name: 'comment_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $post =$postRepository->find($request->get('post_id'));

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $comment->setPost($post);
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash(
                'comment_add_notice',
                'Flash massage: Comment added!'
            );
        }

        return $this->redirectToRoute('post_index', [],Response::HTTP_SEE_OTHER);
    }
}
