<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/user/profile', name: 'user_profile')]
    public function index(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        if ($request->getMethod() === 'POST') {
            $email = $this->getUser()->getUserIdentifier();
            $entityManager = $doctrine->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                throw $this->createNotFoundException(
                    'No user with email: ' . $email
                );
            }

            $user->setName($request->get('name'));
            $user->setEmail($request->get('email'));
            $user->setDescription($request->get('description'));

            $errors = $validator->validate($user);                          //todo

            if (count($errors) > 0) {
                $errorsString = (string)$errors;
                return new Response($errorsString);
            }

            $entityManager->flush();

            $this->addFlash(
                'user_edit_notice',
                'Flash massage: Information update!'
            );
        }

        if (!$user = $this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user/profile_page.html.twig', compact('user'));
    }

    #[Route('/user/{id}/avatar/upload', name: 'user_avatar_upload')]
    public function uploadAvatar(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger): RedirectResponse
    {
        $avatar = $request->files->get('avatar');

        if ($avatar) {
            $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-'. uniqid() .'.' . $avatar->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $avatar->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $request->get('id')]);
            $user->setAvatar($newFilename);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_profile');
    }

    #[Route('/user/change/password', name: 'user_change_password')]
    public function changePassword(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            dd($request->get('password'));
        }
        return $this->render('user/change_password.html.twig');
    }
}
