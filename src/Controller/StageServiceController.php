<?php

namespace App\Controller;


use App\Entity\Stage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[Route('/stage/json')]
class StageServiceController extends AbstractController
{
    #[Route('/', name: 'app_stage_index_json', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {

        $user = $this->getUser();
        $stages = $entityManager
            ->getRepository(Stage::class)
            ->findAll();

        $json = $serializer->serialize($stages, 'json');
        return new Response($json);
    }


    #[Route('/new', name: 'app_stage_new_json')]
    public function new(Request $rq, EntityManagerInterface $entityManager, NormalizerInterface $Normalizer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $stage = new Stage();

        $stage->setPoste($rq->get('poste'));
        $stage->setNomEntreprise($rq->get('nom_entreprise'));
        $stage->setAdresseStage($rq->get('adresse_stage'));
        $stage->setDureeStage($rq->get('duree_stage'));
        $stage->setTypeStage($rq->get('type_stage'));

        $user  = $entityManager->getRepository(User::class)->find(75);
        $stage->setIdUser($user);
        $em->persist($stage);
        $em->flush();
        $jsonContent = $Normalizer->normalize($stage, 'json');
        return new Response(json_encode($jsonContent));
    }
}
