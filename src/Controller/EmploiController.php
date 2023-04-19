<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Form\EmploiType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/emploi')]
class EmploiController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    #[Route('/', name: 'app_emploi_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $emplois = $entityManager
            ->getRepository(Emploi::class)
            ->findAll();

        return $this->render('emploi/index.html.twig', [
            'emplois' => $emplois,
        ]);
    }

    #[Route('/new', name: 'app_emploi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emploi = new Emploi();
        $form = $this->createForm(EmploiType::class, $emploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($emploi);
            $entityManager->flush();

            return $this->redirectToRoute('app_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emploi/new.html.twig', [
            'emploi' => $emploi,
            'form' => $form,
        ]);
    }

    #[Route('/{idEmploi}', name: 'app_emploi_show', methods: ['GET'])]
    public function show(Emploi $emploi): Response
    {
        return $this->render('emploi/show.html.twig', [
            'emploi' => $emploi,
        ]);
    }

    #[Route('/{idEmploi}/edit', name: 'app_emploi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emploi $emploi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmploiType::class, $emploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emploi/edit.html.twig', [
            'emploi' => $emploi,
            'form' => $form,
        ]);
    }

    #[Route('/{idEmploi}', name: 'app_emploi_delete', methods: ['POST'])]
    public function delete(Request $request, Emploi $emploi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emploi->getIdEmploi(), $request->request->get('_token'))) {
            $entityManager->remove($emploi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_emploi_index', [], Response::HTTP_SEE_OTHER);
    }
}
