<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;



class MobileevennementController extends AbstractController
{

  

    #[Route("/addEvenementJSON/new", name: "addEvenementJSON")]
    public function addEvenementJSON(ManagerRegistry $doctrine, Request $req,NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $evenement = new Evenement();
        $evenement->setNomevenement($req->get('nomevenement'));
        $evenement->setDescriptionevenement($req->get('descriptionevenement'));


        $em->persist($evenement);
        $em->flush();

        $jsonContent = $Normalizer->normalize($evenement, 'json');
        return new Response(json_encode($jsonContent));
    }

    #[Route("/evenementmobile/list", name: "list")]
    public function getEvenement(SerializerInterface $serializer)
    { 
        $evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenements = $evenementRepository->findAll();
        $json = $serializer->serialize($evenements, 'json');
        return new Response($json);
    }


    //supprimer
    #[Route("/deleteevenementJSON/{idevenement}", name: "deleteevenementJSON")]
    public function deleteevenementJSON(ManagerRegistry $doctrine, Request $req, $idevenement, NormalizerInterface $Normalizer)
    {

        $em = $doctrine->getManager();
        $evenement = $em->getRepository(Evenement::class)->find($idevenement);
        $em->remove($evenement);
        $em->flush();
        $jsonContent = $Normalizer->normalize($evenement, 'json');
        return new Response("Event deleted successfully " . json_encode($jsonContent));
    }
}
