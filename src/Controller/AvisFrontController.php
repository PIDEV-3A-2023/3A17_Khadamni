<?php

namespace App\Controller;

use App\Entity\Avis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvisFrontController extends AbstractController
{
    #[Route('/avis', name: 'app_avis_front')]
    public function index(): Response
    {
        $avis = $this->getDoctrine()->getRepository(Avis::class)->findAll();

        return $this->render('avis/index_front.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/avis/{idAvis}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }
}
