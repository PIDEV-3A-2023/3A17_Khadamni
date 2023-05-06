<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Rating;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/rating')]
class RatingController extends AbstractController
{
    #[Route('/', name: 'app_rating')]
    public function index(): Response
    {
        return $this->render('rating/index.html.twig', [
            'controller_name' => 'RatingController',
        ]);
    }
    #[Route('/add/{id}/{note}',name: 'app_new_rating')]
    public function addRating(EntityManagerInterface $entityManager,Formation $formation,$note) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $previousRating = $entityManager->getRepository(Rating::class)->findOneBy([
           'iduser' => $user->getIdUser(),
           'idformation' => $formation->getIdFormation()
        ]);
        if (isset($previousRating)) {
            $previousRating->setNote($note);
            $entityManager->flush();
        }
        else {
            $rating = new Rating();
            $rating->setIduser($user);
            $rating->setIdformation($formation);
            $rating->setNote($note);

            $entityManager->persist($rating);
            $entityManager->flush();
        }



        return $this->redirectToRoute('app_show_formation',[
            'id' => $formation->getIdFormation()
        ]);
    }
}
