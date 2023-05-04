<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/admin/formation')]
class AdminFormationController extends AbstractController
{
    #[Route('/', name: 'app_admin_formation')]
    public function index(EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $formations = $em->getRepository(Formation::class)->findAll();
        return $this->render('admin/FormationBack.html.twig',[
            'formations' => $formations
        ]);
    }


    #[Route('/listeInscrits', name: 'app_adminformation_usersinscrits')]
    public function usersInscrits(EntityManagerInterface $em,SerializerInterface $serializer,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $id = $request->get('id');
        $formation = $em->getRepository(Formation::class)->find($id);
        $inscriptions = $em->getRepository(Inscription::class)->findBy([
            'idFormation' => $formation->getIdFormation(),
        ]);
        $inscriptionsWithProgress = [];
        foreach ($inscriptions as $inscription) {
            // calculate progress for each inscription here
            $progress = $this->calculateProgress($inscription,$em);
            if ($progress == 100) $refund = 0;
            else   $refund = number_format($formation->getPrix()-($formation->getPrix()*$progress/100),2);


            // add additional information to inscription array
            $inscriptionWithProgress = [
                'inscription' => $inscription,
                'progress' => $progress,
                'refund' => $refund

            ];

            // add updated inscription to new array
            $inscriptionsWithProgress[] = $inscriptionWithProgress;
        }

        $listeJSON = $serializer->serialize($inscriptionsWithProgress,'json');

        return new Response($listeJSON);


    }

    function calculateProgress($inscription,EntityManagerInterface $entityManager) :float {

        $formation = $entityManager->getRepository(Formation::class)->find($inscription->getIdFormation());

        $duree = $formation->getDuree() * 7;

        $date = $inscription->getDateInscription();
        $today = new \DateTime();

        $finalday = clone $date;

        $finalday->modify("+$duree days");

        $diff1 = $date->diff($finalday);
        $diff2 = $date->diff($today);
        $timepassed = $diff2->y * 364 + $diff2->m * 12 + $diff2->d;
        $totaltime = $diff1->y * 364 + $diff1->m * 12 + $diff1->d;


        $percentage = ( number_format( $timepassed / $totaltime,2)*100);
        if ($percentage > 100) $percentage = 100;

        return $percentage;
    }





}
