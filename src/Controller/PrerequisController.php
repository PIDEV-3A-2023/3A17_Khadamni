<?php

namespace App\Controller;

use App\Entity\Prerequis;
use App\Entity\Stage;
use App\Form\PrerequisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prerequis')]
class PrerequisController extends AbstractController
{
    #[Route('/', name: 'app_prerequis_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $prerequis = $entityManager
            ->getRepository(Prerequis::class)
            ->findAll();

        return $this->render('prerequis/index.html.twig', [
            'prerequis' => $prerequis,
        ]);
    }

    #[Route('/{idStage}/new', name: 'app_prerequis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $idStage): Response
    {
        $prerequi = new Prerequis();
        $form = $this->createForm(PrerequisType::class, $prerequi);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $stage = $entityManager->getRepository(Stage::class)->find($idStage);
            $prerequi->setIdStage($stage);
            $entityManager->persist($prerequi);

            $entityManager->flush();

            return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('prerequis/new.html.twig', [
            'prerequi' => $prerequi,
            'form' => $form,
        ]);
    }

    #[Route('/{idPrerequis}', name: 'app_prerequis_show', methods: ['GET'])]
    public function show(Prerequis $prerequi): Response
    {
        return $this->render('prerequis/show.html.twig', [
            'prerequi' => $prerequi,
        ]);
    }

    #[Route('/{idPrerequis}/edit', name: 'app_prerequis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prerequis $prerequi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrerequisType::class, $prerequi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prerequis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('prerequis/edit.html.twig', [
            'prerequi' => $prerequi,
            'form' => $form,
        ]);
    }

    #[Route('/{idPrerequis}', name: 'app_prerequis_delete', methods: ['POST'])]
    public function delete(Request $request, Prerequis $prerequi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prerequi->getIdPrerequis(), $request->request->get('_token'))) {
            $entityManager->remove($prerequi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prerequis_index', [], Response::HTTP_SEE_OTHER);
    }
}
