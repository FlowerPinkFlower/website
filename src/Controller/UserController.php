<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            $this->DenyAccessUnlessGranted('ROLE_ADMIN');

            return $this->render('user/show.html.twig', [
                'user' => $user
            ]);
        } catch (accessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
    }



    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
         return $this->render('user/index.html.twig', [
             'users' => $userRepository->findAll()
         ]);
        } catch (AccessDeniedException $ex) {
            return $this->redirectToRoute('home');
        }
     }


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
                $user = new User();
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);

                // function mot de passe hash automatique
                if ($form->isSubmitted() && $form->isValid()) { 
                    $user->setPassword(
                    $userPasswordHasher->hashPassword(
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

    // MODIFIER UN UTILISATEUR
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
       
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form
            ]);
    }

    // public function edit(Request $request, User $user, UserRepository $userRepository): Response
    // {
    //     try {
    //         $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
    //         $form = $this->createForm(UserType::class, $user);
    //         $form->handleRequest($request);
    
    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $userRepository->add($user);
    //             return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //         }
    
    //         return $this->renderForm('user/edit.html.twig', [
    //             'user' => $user,
    //             'form' => $form
    //         ]);
    //     } catch (AccessDeniedException $ex) {
    //         return $this->redirectToRoute('home');
    //     }
    // }

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

    // SUPPRIMER UTILISATEUR
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



    // RESTRICTION SUR LUTILISATEUR. UNE PERSONNE NE PEUT PAS AVOIR LE MEME NOM,PRENOM ET EMAIL
    // public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    // {
    //     // Créer une nouvelle instance d'utilisateur à partir des données du formulaire
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Vérifier si un utilisateur existe déjà avec les mêmes informations
    //         $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy([
    //             'email' => $user->getEmail(),
    //             'firstname' => $user->getFirstname(),
    //             'lastname' => $user->getLastname(),
    //         ]);

    //         if ($existingUser !== null) {
    //             $errorMessage = 'Un utilisateur avec ces informations existe déjà.';
    //             // Ajouter une erreur de validation pour empêcher l'enregistrement de l'utilisateur
    //             $validator->context->buildViolation($errorMessage)
    //                 ->atPath('email')
    //                 ->addViolation();
    //             $validator->context->buildViolation($errorMessage)
    //                 ->atPath('firstname')
    //                 ->addViolation();
    //             $validator->context->buildViolation($errorMessage)
    //                 ->atPath('lastname')
    //                 ->addViolation();

    //             return $this->redirectToRoute('register');
    //         }

    //         // Encoder le mot de passe de l'utilisateur
    //         $user->setPassword(
    //             $passwordEncoder->encodePassword($user, $user->getPassword())
    //         );

    //         // Enregistrer l'utilisateur dans la base de données
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         // Rediriger l'utilisateur vers la page de connexion
    //         return $this->redirectToRoute('app_login');
    //     }

    //     // Afficher le formulaire de création de compte
    //     return $this->render('registration/register.html.twig', [
    //         'registrationForm' => $form->createView(),
    //     ]);
    // }
}
