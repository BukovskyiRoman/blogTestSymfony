<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class CommentController extends AbstractController
{
    #[Route('/comment/new', name: 'comment_new')]
    public function new(Request $request,
                        EntityManagerInterface $entityManager,
                        PostRepository $postRepository,
                        ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $post = $postRepository->find($request->get('post_id'));
        $comment = new Comment();

        if (true) {                                                         //todo
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setBody($request->get('body'));

            $errors = $validator->validate($comment);

            if (count($errors) > 0) {
                $errorsString = $errors;
                return new Response($errorsString);
            }

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash(
                'comment_add_notice',
                'Flash massage: Comment added!'
            );
        }

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('comment/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit_comment.html.twig', [
            'comment' => $comment,
            'formComment' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('comment/{id}/update', name: 'comment_update')]
    public function update(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        $id = $request->get('id');
        $entityManager = $doctrine->getManager();
        $comment = $entityManager->getRepository(Comment::class)->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $comment->setBody($request->get('body'));

        $errors = $validator->validate($comment);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return new Response($errorsString);
        }

        $entityManager->flush();

        $this->addFlash(
            'comment_edit_notice',
            'Flash massage: Comment edited!'
        );

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }
}
