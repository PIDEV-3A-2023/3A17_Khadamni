<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Rating;
use App\Entity\User;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/formation/api')]
class FormationAPIController extends AbstractController
{
    #[Route('/', name: 'app_formation_api')]
    public function index(EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {

        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();
        if (count($formations) > 0)
            foreach ($formations as $f) {
                $ratings = $entityManager->getRepository(Rating::class)->findBy([
                    'idformation' => $f->getIdFormation()
                ]);
                $sum = 0;
                if (count($ratings)) {
                    foreach ($ratings as $rating) {
                        $sum += $rating->getNote();
                    }
                    $sum = number_format($sum / count($ratings),2) ;
                }
                $name=$entityManager->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
                $f->setNomFormateur($name);
                $f->setRating($sum);

            }
        $formationsArray = [];
        foreach ($formations as $f) {
            $formationsArray[] = [
                'idFormation' => $f->getIdFormation(),
                'nomFormation' => $f->getNomFormation(),
                'nomFormateur' => $f->getNomFormateur(),
                'idFormateur' => $f->getIdFormateur()->getIdUser(),
                'description' => $f->getDescription(),
                'prix' => $f->getPrix(),
                'duree' => $f->getDuree(),
                'rating' => $f->getRating(),
            ];
        }

        $formationsJSON = $serializer->serialize($formationsArray,'json');
        return new Response($formationsJSON);
    }
    #[Route('/new', name: 'app_formation_api_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {

        $formation = new Formation();
        $nomFormation = $request->get('nomFormation');
        $desc = $request->get('description');
        $prix = $request->get('prix');
        $duree = $request->get('duree');
        $user = $entityManager->getRepository(User::class)->find(75);

        $formation->setNomFormation($nomFormation);
        $formation->setPrix($prix);
        $formation->setDuree($duree);
        $formation->setDescription($desc);
        $formation->setIdFormateur($user);
        $entityManager->persist($formation);
        $entityManager->flush();

        $formationJSON = $serializer->serialize($formation,'json');

        return new Response($formationJSON);


    }
    #[Route('/edit/{idFormation}', name: 'app_formation_api_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer,$idFormation): Response
    {

            $formation = $entityManager->getRepository(Formation::class)->find($idFormation);
            $nomFormation = $request->get('nomFormation');

            $desc = $request->get('description');

            $prix = $request->get('prix');
            $duree = $request->get('duree');


            if(isset($nomFormation))
            $formation->setNomFormation($nomFormation);
            if(isset($prix))
            $formation->setPrix($prix);
            if(isset($duree))
            $formation->setDuree($duree);
            if(isset($desc))
            $formation->setDescription($desc);

            $entityManager->flush();

            $formationJSON = $serializer->serialize($formation,'json');

            return new Response($formationJSON);

    }
    #[Route('/delete/{idFormation}', name: 'app_formation_api_delete', methods: ['GET','POST'])]
    public function delete_formation(Request $request, Formation $formation, EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {

        $formateur = $formation->getIdFormateur();


        $inscriptions = $entityManager->getRepository(Inscription::class)->findBy([
                'idFormation' => $formation->getIdFormation()
            ]);
        if (count($inscriptions)) {
            $stripe = new StripeService();
            foreach ($inscriptions as $inscription) {
                $i = $entityManager->getRepository(Inscription::class)->find($inscription->getIdInscription());
                $customer = $inscription->getIdUser();

                $percentageRefund = number_format($this->calculateProgress($inscription,$entityManager) /100,2);

                if ($percentageRefund < 1)
                    $stripe->refundMoney($customer, $formation, $formateur, 1 - $percentageRefund);
                    $entityManager->remove($i);
                    $entityManager->flush();
                }
            }

            $entityManager->remove($formation);
            $entityManager->flush();

            $formationJSON = $serializer->serialize($formation,'json');

            return new Response($formationJSON);

    }
    #[Route('/userFormations', name: 'app_mesformation_api')]
    public function userFormations(EntityManagerInterface $entityManager,SerializerInterface $serializer,Request $request): Response
    {
        $idUser = $request->get('idUser');
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findBy(['idFormateur' => $idUser]);
        if (count($formations) > 0)
            foreach ($formations as $f) {
                $ratings = $entityManager->getRepository(Rating::class)->findBy([
                    'idformation' => $f->getIdFormation()
                ]);
                $sum = 0;
                if (count($ratings)) {
                    foreach ($ratings as $rating) {
                        $sum += $rating->getNote();
                    }
                    $sum = number_format($sum / count($ratings),2) ;
                }
                $name=$entityManager->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
                $f->setNomFormateur($name);
                $f->setRating($sum);

            }

        $formationsJSON = $serializer->serialize($formations,'json');
        return new Response($formationsJSON);
    }
    #[Route('/userInscriptions', name: 'app_mesinscriptions_api')]
    public function userInscriptions(EntityManagerInterface $entityManager,SerializerInterface $serializer,Request $request): Response
    {
        $idUser = $request->get('idUser');
        $formations = $entityManager->getRepository(Formation::class)->findFormationInscrit($idUser);
        if (count($formations) > 0)
            foreach ($formations as $f) {
                $ratings = $entityManager->getRepository(Rating::class)->findBy([
                    'idformation' => $f->getIdFormation()
                ]);
                $sum = 0;
                if (count($ratings)) {
                    foreach ($ratings as $rating) {
                        $sum += $rating->getNote();
                    }
                    $sum = number_format($sum / count($ratings),2) ;
                }
                $name=$entityManager->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
                $f->setNomFormateur($name);
                $f->setRating($sum);

            }

        $formationsJSON = $serializer->serialize($formations,'json');
        return new Response($formationsJSON);
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
        $timepassed = $diff2->y * 364 + $diff2->m * 30 + $diff2->d;
        $totaltime = $diff1->y * 364 + $diff1->m * 30 + $diff1->d;


        $percentage = ( number_format( $timepassed / $totaltime,2)*100);
        if ($percentage > 100) $percentage = 100;

        return $percentage;
    }









}
