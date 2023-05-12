<?php

namespace App\Controller;



use App\Entity\Emploi;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;


#[Route('/emp/json')]
class EmploiServiceController extends AbstractController
{

    #[Route('/',name: 'app_emploiservice_all_emp')]
    public function all_emp(EntityManagerInterface $entityManager,SerializerInterface $serializer)
    {
        $emplois = $entityManager
            ->getRepository(Emploi::class)
            ->findAll();
        $json = $serializer->serialize($emplois, 'json');
        return new Response($json);
    }

    #[Route('/new', name: 'app_emploi_new_json', methods: ['GET', 'POST'])]
    public function new_json(Request $rq, EntityManagerInterface $em, NormalizerInterface $Normalizer)
    {



        $emploi = new Emploi();

        $emploi->setTitre($rq->get('titre'));
        $emploi->setDescription($rq->get('description'));
        $emploi->setSalaire($rq->get('salaire'));
        $date = new DateTime();
        $emploi->setDatePublication($date);
        $emploi->setNiveauExperience($rq->get('niv'));
        $emploi->setTypeContrat($rq->get('type'));
        $em->persist($emploi);
        $em->flush();
        $jsonContent = $Normalizer->normalize($emploi, 'json');
        return new Response(json_encode($jsonContent));
    }

    #[Route('/emploi/{idEmploi}', name: 'app_emploi_show_json', methods: ['GET'])]
    public function show_json(Emploi $emploi, SerializerInterface $serializer)
    {
        $json = $serializer->serialize($emploi, 'json');
        return new Response($json);
    }

    #[Route('/edit/{idEmploi}', name: 'app_emploi_edit_json', methods: ['GET', 'POST'])]
    public function edit_json($idEmploi, Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $emploi = $entityManager->getRepository(Emploi::class)->find($idEmploi);
        $nomEmploi = $request->get('titre');

        $desc = $request->get('description');

        $salaire = $request->get('salaire');



        if (isset($nomEmploi))
            $emploi->setTitre($nomEmploi);
        if (isset($salaire))
            $emploi->setSalaire($salaire);
        if (isset($desc))
            $emploi->setDescription($desc);

        $entityManager->flush();

        $formationJSON = $serializer->serialize($emploi, 'json');

        return new Response($formationJSON);
    }


    #[Route('/{idEmploi}', name: 'app_emploi_delete_json')]
    public function delete_json(Request $request, $idEmploi, EntityManagerInterface $entityManager, NormalizerInterface $Normalizer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $emploi = $em->getRepository(Emploi::class)->find($idEmploi);

        $em->remove($emploi);
        $em->flush();

        $jsonContent = $Normalizer->normalize($emploi, 'json');
        return new Response("emploi supprimé avec succès" . json_encode($jsonContent));
    }

}
