<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class AdminController extends AbstractController
{
    #[Route('/admin/panel', name: 'admin_panel')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

//        foreach ($users[9]->getPosts() as $post) {
//            dd($post);
//        }
//        dd($users[9]->getPosts());

        return $this->render('admin/admin_panel.html.twig', compact('users'));
    }

    /**
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_panel', [], Response::HTTP_SEE_OTHER);
    }
}
