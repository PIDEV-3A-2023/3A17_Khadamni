<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/adminback', name: 'app_admin_back')]
    public function admin(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
