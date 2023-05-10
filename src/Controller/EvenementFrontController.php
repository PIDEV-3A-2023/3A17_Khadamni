<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Evenement;
use App\Form\AvisType;
use App\Service\MeteoConceptService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpClient\HttpClient;

class EvenementFrontController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement_front')]

    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $orderBy = $request->query->get('order_by', 'date_desc');
        
        $repository = $this->getDoctrine()->getRepository(Evenement::class);
        $queryBuilder = $repository->createQueryBuilder('e');
        
        if ($orderBy === 'date_asc') {
            $queryBuilder->orderBy('e.dateevenement', 'ASC');
        } else {
            $queryBuilder->orderBy('e.dateevenement', 'DESC');
        }
        
        $evenementsPaginated = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            4 // items per page
        );
        
        $totalResults = $evenementsPaginated->getTotalItemCount();
        $currentPage = $evenementsPaginated->getCurrentPageNumber();
        $perPage = $evenementsPaginated->getItemNumberPerPage();

        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($currentPage * $perPage, $totalResults);

        $resultMessage = "Affichage $startResult-$endResult de $totalResults événement(s)";

        return $this->render('evenement/index_front.html.twig', [
            'evenements' => $evenementsPaginated,
            'resultMessage' => $resultMessage
        ]);
    }


    #[Route('/evenement/{idevenement}', name: 'app_evenement_showFront', methods: ['GET'])]
    public function show(Evenement $evenement,EntityManagerInterface $entityManager,Request $request): Response
    {
        $avis = $entityManager
            ->getRepository(Avis::class)
            ->findBy(['idEvenement' => $evenement->getIdevenement()]);

        return $this->render('evenement/show_front.html.twig', [
            'evenement' => $evenement,
            'avis' => $avis,

        ]);
    }


}