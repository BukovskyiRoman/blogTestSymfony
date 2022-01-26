<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AddPostEvent;
use App\Form\CommentType;
use App\Form\PostType;
use App\Message\SmsNotification;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        if ($sort = $request->get('sort')) {
            $queryBuilder = $postRepository->sortByTime($sort);
        } elseif ($id = $request->get('author')) {
            $queryBuilder = $postRepository->findByAuthorId($id);
        } else {
            $queryBuilder = $postRepository->findAll();
        }

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('post/index2.html.twig', [
            'pagination' => $pagination,
        ]);

//        $page = $request->get('page', 1);
//        return $this->render('post/index.html.twig', [
//            'pagination' => $postRepository->getList($page),
//        ]);
    }

    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,
                        ValidatorInterface $validator, MessageBusInterface $bus, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $post = new Post();

        $post->setUserId($user);
        $post->setTitle($request->get('title'));
        $post->setBody($request->get('body'));

        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            $errorsString = $errors;
            return new Response($errorsString);
        }

        $entityManager->persist($post);
        $entityManager->flush();

        $this->sendNotificationAboutNewPost($eventDispatcher, $post);

//        $bus->dispatch(new SmsNotification('Look! I created a message!'));

        $this->addFlash(
            'post_add_notice',
            'Flash massage: Post added!'
        );

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param Post $post
     * @return void
     */
    public function sendNotificationAboutNewPost(EventDispatcherInterface $eventDispatcher, Post $post)
    {
        $eventDispatcher->dispatch(new AddPostEvent($post));
    }

    #[Route('/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    //   #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');            //todo

        if ($post->getUserId() === $this->getUser()) {
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('post/edit.html.twig', [
                'post' => $post,
                'form' => $form,
            ]);
        } else {
            throw new AccessDeniedException('Access denied!');
        }
    }

    #[Route('/{id}/update', name: 'post_update')]
    public function update(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        $id = $request->get('id');
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);


        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
            );
        }

        $post->setTitle($request->get('title'));
        $post->setBody($request->get('body'));

        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return new Response($errorsString);
        }

        $entityManager->flush();

        $this->addFlash(
            'post_edit_notice',
            'Flash massage: Post edited!'
        );

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }
}
