<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Service\MailerService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation_index')]
    public function index(EntityManagerInterface $em,ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();
        if (isset($user))
           $formations= $em->getRepository(Formation::class)->findAllExceptMine($user->getIdUser());
        else {
            $formations = $em
                ->getRepository(Formation::class)
                ->findAll();
        }

        if (count($formations) > 0)
        foreach ($formations as $f) {
            $ratings = $em->getRepository(Rating::class)->findBy([
                'idformation' => $f->getIdFormation()
            ]);
            $sum = 0;
            if (count($ratings)) {
                foreach ($ratings as $rating) {
                    $sum += $rating->getNote();
                }
                $sum = number_format($sum / count($ratings),2) ;
            }
            $name=$doctrine->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
            $f->setNomFormateur($name);
            $f->setRating($sum);

        }
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }



    #[Route('/mesformations',name:'app_mes_formations')]
    public function MesFormations(ManagerRegistry $doctrine ,Request $request,Session $session) : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();

        $stripe = new StripeService();
        $acc=$stripe->retriveAccount($user->getEmail());

        $ref = $request->query->get('from');

        $encours = False;
        if ($ref == 'accountlink') {
            $acc->details_submitted ? $encours = True : $encours = false;
        }



        if (!$acc->payouts_enabled) {
            return  $this->renderForm('formation/mesformations.html.twig', [
                'verified' => 'False',
                'encours' => $encours
            ]);
        }

            $id = $user->getIdUser();
            $formations = $doctrine
                ->getRepository(Formation::class)
                ->findBy(['idFormateur'=>$id]);
            foreach ($formations as $f) {
                $name=$doctrine->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
                $f->setNomFormateur($name);
            }
            $formation= new Formation();
            $form = $this->createForm(FormationType::class, $formation,[
                'method'=> 'POST'
            ]);
            $form->handleRequest($request);

            $entityManager = $doctrine->getManager();
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($formation);
                $entityManager->flush();

                return $this->redirectToRoute('app_mes_formations', [], Response::HTTP_SEE_OTHER);
            }


        return  $this->renderForm('formation/mesformations.html.twig', [
            'formations' => $formations,
            'formation' => $formation,
            'form' => $form,
            'verified' => 'True'
        ]);
    }

    #[Route('/activate',name: 'app_payment_activate')]
    public function ActivatePayment(Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $ref = $request->headers->get('referer');
        if (!strpos($ref,'mesformations') or !isset($ref))  {
            return $this->redirectToRoute('app_home');
        }


        $stripe = new StripeService();
        $acc=$stripe->retriveAccount($user->getEmail());
        $return_url = $this->generateUrl('app_mes_formations', ['from'=>'accountlink'],UrlGeneratorInterface::ABSOLUTE_URL);
        $account_link = $stripe->finishSignUp($acc,$return_url);

        return new RedirectResponse($account_link->url);
    }

    #[Route('/checkout/{id}',name: 'app_formation_checkout')]
    public function checkout(Request $request,Formation $formation,EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $formateur = $em->getRepository(User::class)->find($formation->getIdFormateur());
        $user = $this->getUser();
        $formations = $em->getRepository(Formation::class)->findFormationInscrit($user->getIdUser());

        if ($user->getIdUser() == $formateur->getIdUser() || in_array($formation,$formations)  ) {
            $session = $request->getSession();
            $session->getFlashBag()->add('error', 'Vous ne pouvez pas s\'inscrire à cette formation !');
            return $this->redirectToRoute('app_formation_index',[
           ]);
        }
        $success_url = $this->generateUrl('app_success', ['from'=>'checkout'],UrlGeneratorInterface::ABSOLUTE_URL);

        // Set session variablecomp
        $session = $request->getSession();
        $session->set('formationID', $formation->getIdFormation());


        $stripe = new StripeService();
        $checkout =$stripe->CreateCheckoutSession($this->getUser(),$formation,$formateur->getEmail(),$success_url);
        $session->set('checkout_session_id',$checkout->id);
        return new RedirectResponse($checkout->url);


    }

    #[Route('/success',name: 'app_success')]
    public function onPaiementSuccess(Request $request,EntityManagerInterface $entityManager)  {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $ref = $request->query->get('from');
        $user = $this->getUser();
        if ($ref == 'checkout') {
            $session = $request->getSession();
            $IDformation = $session->get('formationID');

            $nf = $entityManager->getRepository(Formation::class)->find($IDformation);
            $inscri = new Inscription();
            $inscri->setIdFormation($nf);
            $inscri->setIdUser($user);
            $inscri->setDateInscription(new \DateTime());


            $entityManager->persist($inscri);
            $entityManager->flush();

            $session->set('formationID',$IDformation);
             return $this->redirectToRoute('app_mailer',[

             ]);

        }
        return $this->redirectToRoute('app_formation_index',[

        ]);


    }
    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user =$this->getUser();

        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formation->setIdFormateur($user);
            $entityManager->persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('app_mes_formations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }



    #[Route('/inscrit',name: 'app_inscrit')]
    public function inscritFormation(EntityManagerInterface $entityManager,Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();

        $formations = $entityManager->getRepository(Formation::class)->findFormationInscrit($user->getIdUser());
        if (count($formations)) {
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
        }

        return $this->render('formation/inscritFormation.html.twig', [
            'formations' => $formations,
        ]);

    }

    #[Route('/refund/{id}',name: 'app_formation_refund')]
    public function refundInscription(EntityManagerInterface $entityManager,Formation $formation) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $formateur = $formation->getIdFormateur();
        $inscription = $entityManager->getRepository(Inscription::class)->findOneBy([
            'idFormation' => $formation,
            'idUser' => $user
        ]);


        $today = new \DateTime();
        $days = $today->diff($inscription->getDateInscription())->d;
        $duree = $formation->getDuree()*7;
        $stripe = new StripeService();


        $percentageRefund = number_format($days / $duree,2) ;

        if ($percentageRefund <1) {
            if ($days < 2 )
                $stripe->refundMoney($user,$formation,$formateur,1);
            else $stripe->refundMoney($user,$formation,$formateur,1-$percentageRefund);
        }



        $entityManager->remove($inscription);
        $entityManager->flush();


        return $this->redirectToRoute('app_show_formation',[
            'id' => $formation->getIdFormation(),
        ]);

    }
    #[Route('/{idFormation}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mes_formations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{idFormation}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $formateur = $formation->getIdFormateur();
        $ref = $request->headers->get('referer');

        if ($this->isCsrfTokenValid('delete'.$formation->getIdFormation(), $request->request->get('_token'))) {
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
        }


        if ( strpos($ref,'admin'))
            return $this->redirectToRoute('app_admin_formation',[]);
        else  return $this->redirectToRoute('app_mes_formations', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/search',name: 'searchFormationName',methods:['GET'])]
    public function searchFormations(Request $req,NormalizerInterface $normalizer,FormationRepository $fr)
    {
        $user = $this->getUser();
        $searchValue = $req->get('searchValue');
        $minPrix = $req->get('minPrix');
        $maxPrix = $req->get('maxPrix');
        $minDur = $req->get('minDuree');
        $maxDur = $req->get('maxDuree');


        $formations = $fr->findformationByX($searchValue,$minPrix,$maxPrix,$minDur,$maxDur,$user->getIdUser());
        $jsonContent = $normalizer->normalize($formations,'json');
        $retour = json_encode($jsonContent);

        return new Response($retour);

    }


    #[Route('/show/{id}',name: 'app_show_formation')]
    function showFormation(Request $request,EntityManagerInterface $entityManager,Formation $formation,ManagerRegistry $doctrine) {

        $name=$doctrine->getRepository(Formation::class)->getNomFormateur($formation->getIdFormateur());
        $formation->setNomFormateur($name);
        $user = $this->getUser();
        $allInscri = $entityManager->getRepository(Inscription::class)->findBy([
            'idFormation' => $formation
        ]);
        isset($allInscri) ? $nbInscrits = count($allInscri) : $nbInscrits = 0;

        $mine = False;
        $inscri = False;
        $progress = 0;
        $userRating = 0;
        if (isset($user)) {
            if ($user->getIdUser() == $formation->getIdFormateur()->getIdUser()) $mine = True;
            else {
                $inscription = $doctrine->getRepository(Inscription::class)->findOneBy([
                    'idUser' => $user->getIdUser(),
                    'idFormation' => $formation->getIdFormation(),
                ]);
                if  (isset($inscription)) {
                    $inscri = True;
                    $userRating = $entityManager->getRepository(Rating::class)->findOneBy([
                      'idformation' => $formation->getIdFormation(),
                      'iduser' => $user->getIdUser()
                    ]);
                    isset($userRating) ? $userRating = $userRating->getNote() : $userRating = 0;

                    $progress = $this->calculateProgress($inscription,$entityManager);

                }
            }


        }
        $stats = $this->getRatings($formation,$entityManager);


        return $this->render('formation/show.html.twig',[
           'formation' => $formation,
            'inscrit' => $inscri,
            'mine' => $mine,
            'progressPercentage' => $progress,
            'nbInscrits' => $nbInscrits,
            'stats' => $stats,
            'userRating' => $userRating

        ]);
    }

    function getRatings($formation,EntityManagerInterface $entityManager) {
        $ratings = $entityManager->getRepository(Rating::class)->findBy([
           'idformation' => $formation->getIdFormation()
        ]);
        $total = count($ratings);
        $sum = 0; $perc_5stars=0; $perc_4stars=0; $perc_3stars=0; $perc_2stars=0; $perc_1stars=0;
        if ($total)  {
            $total_5_stars = count(array_filter($ratings, function ($rating) {
                return $rating->getNote() === 5;
            }));
            $perc_5stars =  floor(($total_5_stars/$total)*100);
            $total_4_stars = count(array_filter($ratings, function ($rating) {
                return $rating->getNote() === 4;
            }));
            $perc_4stars =  floor(($total_4_stars/$total)*100);
            $total_3_stars = count(array_filter($ratings, function ($rating) {
                return $rating->getNote() === 3;
            }));
            $perc_3stars =  floor(($total_3_stars/$total)*100);
            $total_2_stars = count(array_filter($ratings, function ($rating) {
                return $rating->getNote() === 2;
            }));
            $perc_2stars =  floor(($total_2_stars/$total)*100);
            $total_1_stars = count(array_filter($ratings, function ($rating) {
                return $rating->getNote() === 1;
            }));
            $perc_1stars = floor(($total_1_stars/$total)*100);
            foreach ($ratings as $rating) {
                $sum += $rating->getNote();
            }
            $sum = number_format($sum / $total,2) ;

        }
        return [
            'total' => $total,
            'rating' => $sum,
            'perc_5_stars' => $perc_5stars,
            'perc_4_stars' => $perc_4stars,
            'perc_3_stars' => $perc_3stars,
            'perc_2_stars' => $perc_2stars,
            'perc_1_stars' => $perc_1stars,
        ];
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


