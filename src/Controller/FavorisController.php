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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();

        $favoris = $entityManager
            ->getRepository(Favoris::class)
            ->findBy(["idUser" => $user]);

        return $this->render('favoris/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }
    #[Route('/{idFavoris}', name: 'app_favoris_delete', methods: ['POST'])]
    public function delete(Request $request, Favoris $favori, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $favori->getIdFavoris(), $request->request->get('_token'))) {
            $entityManager->remove($favori);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/favorite/search', name: 'favorite_search')]

    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {

        $searchTerm = $request->query->get('searchTerm');
        $results = $entityManager
            ->getRepository(Favoris::class);

        return $this->json([
            'results' => $results
        ]);
    }
}
