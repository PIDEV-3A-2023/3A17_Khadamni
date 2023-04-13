<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\SuiviReclamation;
use App\Form\SuiviReclamationType;
use Symfony\Component\Mime\Message;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints\Valid;
#[Route('/reclamation')]
class ReclamationController extends AbstractController
{


    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }


  #[Route('/front', name: 'app_reclamation_front', methods: ['GET'])]
    public function front(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('reclamation/front.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }


  #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
    
    // Set the default value for the dateReclamation field
    $date = new DateTime();
    $formattedDate = $date->format('Y-m-d');
    $reclamation->setDateReclamation(new \DateTime($formattedDate));

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
        
            $entityManager->persist($reclamation);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_reclamation_front', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
    

    #[Route('/{idReclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{idReclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idReclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdReclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('reclamation/repondre/{idReclamation}', name: 'app_reclamation_repondre', methods: ['GET', 'POST'])]
    public function repondre(Request $request, EntityManagerInterface $entityManager, $idReclamation)
    {
        $suiviReclamation = new SuiviReclamation();
        $form = $this->createForm(SuiviReclamationType::class, $suiviReclamation);
        
        $reclamation = $entityManager
            ->getRepository(Reclamation::class)
            ->find($idReclamation);
        
        $suiviReclamation = $entityManager
            ->getRepository(SuiviReclamation::class)
            ->findOneBy([
                'idReclamation' => $idReclamation
            ]);
        
        if (!$suiviReclamation) {
            $suiviReclamation = new SuiviReclamation();
            $suiviReclamation->setIdReclamation($reclamation);
            $entityManager->persist($suiviReclamation);
        }
        
        $form = $this->createForm(SuiviReclamationType::class, $suiviReclamation);
    
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
        
   
        
            $entityManager->persist($suiviReclamation);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_reclamation_front', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('reclamation/repondre.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }
    


/**
 * @Route("/reclamation/messages/{idReclamation}", name="app_reclamation_messages")
 */
public function messages(Request $request, $idReclamation)
{
    $reclamation = $this->getDoctrine()
        ->getRepository(Reclamation::class)
        ->find($idReclamation);

    $suiviReclamation = $this->getDoctrine()
        ->getRepository(SuiviReclamation::class)
        ->findOneBy([
            'idReclamation' => $idReclamation
        ]);

    $form = $this->createFormBuilder()
        ->add('avis', TextareaType::class, [
            'label' => 'Avis'
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Save'
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();

        $suiviReclamation->setAvis($data['avis']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($suiviReclamation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reclamation_front');
    }

    return $this->render('reclamation/messages.html.twig', [
        'reclamation' => $reclamation,
        'suiviReclamation' => $suiviReclamation,
        'form' => $form->createView()
    ]);
}



    
    
}