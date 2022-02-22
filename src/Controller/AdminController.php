<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin/panel', name: 'admin_panel')]
    public function index(UserRepository $userRepository): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
        $users = $userRepository->findAll();

        //dd($users);
//        foreach ($users as $post) {
//            dd($post->getRoles());
//        }


        return $this->render('admin/admin_panel.html.twig', compact('users'));
    }

    /**
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/admin/delete/user/{id}', name: 'user_delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_panel', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/change_role/{id}', name: 'set_admin', methods: 'POST')]
    public function setAdminRole(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['id' => $request->get('id')]);

        $roles = $user->getRoles();
        $index = array_search('ROLE_ADMIN', $roles);

        if (!is_int($index)) {
            $user->setRole('ROLE_ADMIN');
        } else {
            unset($roles[$index]);
            $user->setRoles(array_values($roles));
        }
        $entityManager->flush();

        return new Response(Response::HTTP_OK);
    }
}
