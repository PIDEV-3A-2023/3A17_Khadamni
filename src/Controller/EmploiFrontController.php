<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmploiFrontController extends AbstractController
{
    #[Route('/emploi/front', name: 'app_emploi_front')]
    public function affiche(): Response
    {
        return $this->render('emploi_front/emploiF.html.twig', [
            'controller_name' => 'EmploiFrontController',
        ]);
    }

    #[Route('/emploi/detail', name: 'emploi_details')]
    public function detail(): Response
    {
        return $this->render('emploi_front/detailEmp.html.twig', [
            'controller_name' => 'EmploiFrontController',
        ]);
    }
}
