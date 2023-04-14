<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmploiFrontController extends AbstractController
{
    #[Route('/emp/front', name: 'app_emploi_front')]
    public function affiche(): Response
    {
        return $this->render('emploi/emploiF.html.twig', [
            'controller_name' => 'EmploiFrontController',
        ]);
    }

    #[Route('/emp/detail', name: 'emploi_details')]
    public function detail(): Response
    {
        return $this->render('emploi/detailEmp.html.twig', [
            'controller_name' => 'EmploiFrontController',
        ]);
    }
}
