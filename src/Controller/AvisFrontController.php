<?php

namespace App\Controller;


use App\Entity\Avis;
use App\Entity\Evenement;
use App\Entity\User;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;


class AvisFrontController extends AbstractController
{   

    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    #[Route('/avis', name: 'app_avis_front')]

    public function index(Request $request): Response
    {
        $orderBy = $request->query->get('order_by', 'date_desc');
        
        $repository = $this->getDoctrine()->getRepository(Avis::class);
        $queryBuilder = $repository->createQueryBuilder('a');
        
        if ($orderBy === 'date_asc') {
            $queryBuilder->orderBy('a.dateCreation', 'ASC');
        } else {
            $queryBuilder->orderBy('a.dateCreation', 'DESC');
        }
        
        $avisPaginated = $this->paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            4 // items per page
        );

        $totalResults = $avisPaginated->getTotalItemCount();
        $currentPage = $avisPaginated->getCurrentPageNumber();
        $perPage = $avisPaginated->getItemNumberPerPage();

        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($currentPage * $perPage, $totalResults);

        $resultMessage = "Affichage $startResult-$endResult de $totalResults avis";

        return $this->render('avis/index_front.html.twig', [
            'avis' => $avisPaginated,
            'resultMessage' => $resultMessage,
        ]);

    }

    /*
    public function index(Request $request)
    {
        $orderBy = $request->query->get('order_by', 'date_desc');

        $repository = $this->getDoctrine()->getRepository(Avis::class);
        $queryBuilder = $repository->createQueryBuilder('a');
        if ($orderBy === 'date_asc') {
            $queryBuilder->orderBy('a.dateCreation', 'ASC');
        } else {
            $queryBuilder->orderBy('a.dateCreation', 'DESC');
        }
        $query = $queryBuilder->getQuery();
        $paginator = $this->get('knp_paginator');
        $avis = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        $totalResults = $evenementsPaginated->getTotalItemCount();
        $currentPage = $evenementsPaginated->getCurrentPageNumber();
        $perPage = $evenementsPaginated->getItemNumberPerPage();

        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($currentPage * $perPage, $totalResults);

        $resultMessage = "Affichage $startResult-$endResult de $totalResults avis";

        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
            'resultMessage' => $resultMessage,
        ]);
    }*/


    

    #[Route('/avis/new', name: 'app_avis_create', methods: ['GET', 'POST'])]
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set any additional properties on $avis if needed
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_front');
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/avis/ajouter/{idEvenement}', name: 'app_ajouter_avis')]
    public function createNewAvis(Request $request,$idEvenement,EntityManagerInterface $entityManager): Response
    {

        $comment = $request->request->get('comment');
        $evenement = $entityManager->getRepository(Evenement::class)->find($idEvenement);
        $user = $entityManager->getRepository(User::class)->find(75);
        $date = new \DateTime();
        $avis = new Avis();
        $avis->setCommentaire($comment);
        $avis->setNote(10);
        $avis->setDateCreation($date);
        $avis->setIdEvenement($evenement);
        $avis->setIdUtilisateur($user);

        $LesAvis = $entityManager
            ->getRepository(Avis::class)
            ->findAll();

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->redirectToRoute('app_evenement_showFront',[
            'idevenement' => $idEvenement,
            'avis' => $LesAvis
        ]);

    }

}
