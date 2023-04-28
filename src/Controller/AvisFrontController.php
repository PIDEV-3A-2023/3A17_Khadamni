<?php

namespace App\Controller;

use App\Entity\Avis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

class AvisFrontController extends AbstractController
{
    #[Route('/avis', name: 'app_avis_front')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $$queryBuilder = $this->getDoctrine()->getRepository(Avis::class)->createQueryBuilder('a');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('avis/index_front.html.twig', [
            'avis' => $pagination,
        ]);
    }

    #[Route('/avis/{idAvis}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }

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
}
