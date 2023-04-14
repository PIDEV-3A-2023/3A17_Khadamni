<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/evenement/new', name: 'app_evenement_createFront', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $evenement = new Evenement();

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $inviter = $request->request->get('inviter');
            $date = $request->request->get('date');

            // Vérifier que les champs ne sont pas vides
            if (empty($nom) || empty($description) || empty($inviter) || empty($date)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->redirectToRoute('app_evenement_createFront');
            }

            // Vérifier que la date est valide
            if (!\DateTime::createFromFormat('Y-m-d', $date)) {
                $this->addFlash('error', 'La date n\'est pas valide.');
                return $this->redirectToRoute('app_evenement_createFront');
            }

            // Si tout est OK, créer l'événement
            $evenement->setNomevenement($nom);
            $evenement->setDescriptionevenement($description);
            $evenement->setInviter($inviter);
            $evenement->setDateevenement(new \DateTime($date));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
            return $this->redirectToRoute('app_evenement_front');
        }

        return $this->render('evenement/edit.html.twig');
    }

    #[Route('/evenement/{idevenement}', name: 'app_evenement_showFront', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show_front.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}

