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
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $users = $userRepository->createQueryBuilder('u')
                ->orderBy('u.lastname', 'ASC')
                ->getQuery()
                ->getResult();
    
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'users' => $users
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }




    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $users = $userRepository->findBy([], ['lastname' => 'ASC']);
            return $this->render('user/index.html.twig', [
                'users' => $users
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }


    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }



    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) { 
                $user->setPassword(
                    $this->passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $userRepository->add($user);
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('user/new.html.twig', [
                'user' => $user,
                'form' => $form
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }


    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $userRepository->add($user);
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
    
            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form
            ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }
    


    

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        try {
            $this->DenyAccessUnlessGranted('ROLE_ADMIN');
 
         if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
             $userRepository->remove($user);
         }
 
         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        } 
        catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }
}
