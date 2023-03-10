<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    // #[Route('/', name: 'app_user_index', methods: ['GET'])]
    // public function index(UserRepository $userRepository): Response
    // {
    //     try {
    //         $this->denyAccessUnlessGranted('ROLE_ADMIN');
    //         // $users = $userRepository->findBy([], ['lastname' => 'ASC']);

    //      return $this->render('user/index.html.twig', [
    //          'users' => $userRepository->findAll()
    //      ]);
    //     } catch (AccessDeniedException $ex) {
    //         return $this->redirectToRoute('home');
    //     }
    // }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $query = $request->query->get('q');
            $users = $userRepository->search($query);

            return $this->render('user/index.html.twig', [
                'users' => $users,
                'query' => $query
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }

        /**
     * @Route("/utilisateur/{id}", name="utilisateur", methods={"GET"})
     */
    public function utilisateur(User $user): Response
    {
        
            return $this->render('user/test.html.twig', [
                'user' => $user
                
            ]);
            return $this->redirectToRoute('home');
        
    }


    // public function index(UserRepository $userRepository, Request $request): Response
    // {
    //     try {
    //         $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
    //         $query = $request->query->get('q');
    //         if ($query !== null) {
    //             $users = $userRepository->search($query);
    //         }else{
    //             $users = $userRepository->findBy([], ['lastname' => 'ASC', 'firstname' => 'ASC']);

    //         }
    
    //         return $this->render('user/index.html.twig', [
    //             'users' => $users,
    //             'query' => $query
    //         ]);
    //     } catch (AccessDeniedException $ex) {
    //         return $this->redirectToRoute('home');
    //     }
    // }


    // #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, UserRepository $userRepository): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userRepository->save($user, true);

    //         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }


    
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                // Hash the password
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
    
                $userRepository->save($user, true);
    
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
    
            return $this->renderForm('user/new.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }

    

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        try {
            $this->DenyAccessUnlessGranted('ROLE_ADMIN');

            return $this->render('user/show.html.twig', [
                'user' => $user
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }


    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        try {
            $this->DenyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        } 
        catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }
}