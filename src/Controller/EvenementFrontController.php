<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementFrontController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement_front')]

    public function index(): Response
    {
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        return $this->render('evenement/index_front.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenement/{idevenement}', name: 'app_evenement_showFront', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show_front.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}
