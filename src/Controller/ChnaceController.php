<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChnaceController extends AbstractController
{
    #[Route('/chnace', name: 'app_chnace')]
    public function index(): Response
    {
        return $this->render('chnace/index.html.twig', [
            'controller_name' => 'ChnaceController',
        ]);
    }
}
