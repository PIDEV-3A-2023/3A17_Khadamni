<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Candidaturestage;
use Doctrine\ORM\EntityManager;
use App\Entity\Formation;
use App\Entity\Stage;
use App\Entity\User;
use App\Form\StageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stage')]
class StageController extends AbstractController
{
    #[Route('/', name: 'app_stage_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stages = $entityManager
            ->getRepository(Stage::class)
            ->findAll();

        return $this->render('stage/index.html.twig', [
            'stages' => $stages,
        ]);
    }

    #[Route('/new', name: 'app_stage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stage = new Stage();
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stage);
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stage/new.html.twig', [
            'stage' => $stage,
            'form' => $form,
        ]);
    }

   /* #[Route('/{idStage}', name: 'app_stage_show', methods: ['GET'])]
    public function show(Stage $stage): Response
    {
        return $this->render('stage/show.html.twig', [
            'stage' => $stage,
        ]);
    }*/

    #[Route('/{idStage}/edit', name: 'app_stage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stage/edit.html.twig', [
            'stage' => $stage,
            'form' => $form,
        ]);
    }

    #[Route('/{idStage}', name: 'app_stage_delete', methods: ['POST'])]
    public function delete(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stage->getIdStage(), $request->request->get('_token'))) {
            $entityManager->remove($stage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
    }
  
   
    
    #[Route('/front', name: 'app_stage_front', methods: ['GET'])]
    public function indexfront(EntityManagerInterface $entityManager): Response
    {   
        $stages = $entityManager
            ->getRepository(Stage::class)
            ->findAll();
    
        return $this->render('stage/stagefront.html.twig', [
            'stages' => $stages,
        ]);
    }
       
    #[Route('/postuler/{idStage}', name: 'app_stage_postuler', methods: ['GET'])]
    public function postuler(EntityManagerInterface $entityManager,Stage $stage): Response
    {   $candidature = new Candidaturestage();
        $candidature->setIdStage($stage);
        $user=$this->getDoctrine()->getRepository(User::class)->find(1);

        $candidature->setIdUser($user);
        $entityManager->persist($candidature);
        $entityManager->flush();
        
            
        return $this->redirectToRoute('app_stage_front', [], Response::HTTP_SEE_OTHER);

    }
    
    #[Route('/candidats/{idStage}', name: 'app_stage_candidats', methods: ['GET'])]
    public function candidats(int $idStage,EntityManagerInterface $entityManager): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
    
        // Récupérer la liste des candidatures par ID de stage
        $candidatures = $entityManager->getRepository(Candidaturestage::class)->findBy(['idStage' => $idStage]);
    
        // Construire la requête pour récupérer les utilisateurs correspondants
        $qb = $entityManager->createQueryBuilder();
        $qb->select('u.nom, u.prenom,u.email')
            ->from('App\Entity\User', 'u')
            ->innerJoin('App\Entity\Candidaturestage', 'c', 'WITH', 'u.idUser = c.idUser')
            ->where('c.idStage = :idStage')
            ->setParameter('idStage', $idStage);
    
        // Récupérer la liste des utilisateurs correspondants
        $users = $qb->getQuery()->getResult();
    
        // Rendre la vue avec la liste des utilisateurs correspondants
        return $this->render('stage/affichercandidats.html.twig', [
            'candidats' => $users,
        ]);
    }
    
   
}


