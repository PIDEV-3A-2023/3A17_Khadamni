<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\SuiviReclamation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;



#[Route('/reclamation/service', name: 'app_reclamation_service')]
class ReclamationServiceController extends AbstractController
{


    #[Route('/afficher', name: 'app_reclamation_JSON')]
    public function allReclamation(Request $request,EntityManagerInterface $em,SerializerInterface $serializer)
    {
        $reclamations = $em
            ->getRepository(Reclamation::class)
            ->findAll();


        $formatted = $serializer->serialize($reclamations,'json');
        return new Response($formatted);
    }


    #[Route('/new', name: 'ajouter_reclamation_json')]

    public function ajouter_reclamation_json(Request $request,EntityManagerInterface $em,SerializerInterface $serializer)
    {
        $reclamation = new Reclamation();

        $description = $request->query->get("description");
        $typeReclamation=$request->query->get("typeReclamation");
        $date=new \DateTime ('now');

        $user = $em->getRepository(User::class)->find(75);
        $reclamation->setIdUser($user);

        $reclamation ->setDescription($description);
        $reclamation ->setTypeReclamation($typeReclamation);
        $reclamation ->setDateReclamation($date);

        $em->persist($reclamation);
        $em->flush();

        $suivi = new SuiviReclamation();
        $suivi->setEtatReclamation("non traitee");
        $suivi->setIdReclamation($reclamation);
        $em->persist($suivi);
        $em->flush();



        $rec = $serializer->serialize($reclamation,'json');
        return new Response($rec);

    }


    #[Route('/delete', name: 'delete_reclamation_JSON')]
    public function deleteRecLamationAction(Request $request,EntityManagerInterface $em,SerializerInterface $serializer) {

        $id = $request->get ("id");
        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        if (isset($reclamation) ) {
            $em->remove ($reclamation);
            $em->flush ();

            return new Response('reclamation has been deleted successfully');
        }

        return new Response('reclamation not found');
    }


    #[Route('/update', name: 'update_reclamation_JSON')]
    public function modifierRecLamationAction(Request $request,EntityManagerInterface $em,SerializerInterface $serializer) {

        $id = $request->get("id");
        $desc = $request->get("description");
        $type = $request->get("typeReclamation");
        $reclamation = $em
            ->getRepository(Reclamation::class)
            ->find($id);
        if (!isset($reclamation))
            return new Response("Reclamation non trouvée");

        if(isset($desc) and strlen($desc) > 3)
        $reclamation ->setDescription($desc);
        if(isset($type) and strlen($type) > 4)
        $reclamation ->setTypeReclamation($type);


        $em->flush();


        return new Response("Reclamation modifiée avec succées");

    }





    #[Route('/detail', name: 'detail_reclamation_JSON', methods: ['GET'])]
    public function detailRecLamationAction(Request $request,EntityManagerInterface $em,SerializerInterface $serializer)
    {
        $id = $request->get("id");


        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        if (!isset($reclamation))
            return new Response('reclamation non trouvée');

       $formatted = $serializer->serialize($reclamation,'json');

        return new Response($formatted);
    }
}
