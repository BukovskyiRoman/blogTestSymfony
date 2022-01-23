<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/new', name: 'comment_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $post = $postRepository->find($request->get('post_id'));


        $comment = new Comment();

        $form = $this->createFormBuilder($comment)
        ->add('body', TextType::class)
        ->getForm();
        $form->handleRequest($request);

        if (true) {
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setBody($request->get('body'));
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash(
                'comment_add_notice',
                'Flash massage: Comment added!'
            );
        }

        return $this->redirectToRoute('post_index', [],Response::HTTP_SEE_OTHER);
    }

    #[Route('comment/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }
}
