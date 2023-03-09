<?php

namespace App\Controller;



use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

use Symfony\Contracts\Translation\TranslatorInterface;


class RegistrationController extends AbstractController
{
    // #[Route('/register', name: 'app_register')]
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification si un utilisateur avec le même nom, prénom et date de naissance existe déjà
            $existingUser = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                    'birthdate' => $user->getBirthdate()
                ]);
            if ($existingUser) {
                $this->addFlash('danger', 'Un utilisateur avec le même nom, prénom et date de naissance existe déjà.');
                return $this->redirectToRoute('app_register');
            }

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
