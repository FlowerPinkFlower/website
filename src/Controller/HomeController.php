<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // #[Route('/base', name: 'app_home')]
    // public function index(): Response
    // {
    //     return $this->render('home/index.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }

     /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }


     /**
     * @Route("/erreur", name="erreur")
     */
    public function erreur(SubCategoryRepository $subCateRepo, CategoryRepository $cateRepo): Response
    {
        return $this->render('home/erreur.html.twig');
    }
}
