<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Form\FavorisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favoris')]
class FavorisController extends AbstractController
{
    #[Route('/', name: 'app_favoris_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $favoris = $entityManager
            ->getRepository(Favoris::class)
            ->findAll();

        return $this->render('favoris/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }

    #[Route('/new', name: 'app_favoris_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $favori = new Favoris();
        $form = $this->createForm(FavorisType::class, $favori);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($favori);
            $entityManager->flush();

            return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('favoris/new.html.twig', [
            'favori' => $favori,
            'form' => $form,
        ]);
    }

    #[Route('/{idFavoris}', name: 'app_favoris_show', methods: ['GET'])]
    public function show(Favoris $favori): Response
    {
        return $this->render('favoris/show.html.twig', [
            'favori' => $favori,
        ]);
    }

    #[Route('/{idFavoris}/edit', name: 'app_favoris_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Favoris $favori, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FavorisType::class, $favori);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('favoris/edit.html.twig', [
            'favori' => $favori,
            'form' => $form,
        ]);
    }

    #[Route('/{idFavoris}', name: 'app_favoris_delete', methods: ['POST'])]
    public function delete(Request $request, Favoris $favori, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favori->getIdFavoris(), $request->request->get('_token'))) {
            $entityManager->remove($favori);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
    }
}
