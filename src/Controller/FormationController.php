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
use Doctrine\ORM\EntityManager;
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
                $sum = round($sum / count($ratings),2) ;
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
                    $sum = round($sum / count($ratings),2) ;
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
    public function refundInscription(EntityManagerInterface $entityManager,Formation $formation,Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $formateur = $formation->getIdFormateur();
        $inscription = $entityManager->getRepository(Inscription::class)->findOneBy([
            'idFormation' => $formation,
            'idUser' => $user
        ]);


        $today = new \DateTime();
        $days = $today->diff($inscription->getDateInscription())->d;


        if ($days <3 ) {
            $stripe = new StripeService();
            if ($days == 2) {
                $hours = $today->diff($inscription->getDateInscription())->h;
                $minutes = $today->diff($inscription->getDateInscription())->i;
                $seconds = $today->diff($inscription->getDateInscription())->s;
                if (!($seconds || $minutes || $hours))
                $stripe->refundMoney($user,$formation,$formateur,1);
            }
            else $stripe->refundMoney($user,$formation,$formateur,1);
            }

        $entityManager->remove($inscription);
        $entityManager->flush();


        return $this->redirectToRoute('app_formation_index',[]);

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
        $formateur = $this->getUser();
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


                    $date = $inscription->getDateInscription();
                    $today = new \DateTime();
                    $diff = $today->diff($date)->d;
                    $duree = $formation->getDuree();

                    $percentageRefund = round($diff / $duree * 7,2);
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
            return $this->redirectToRoute('app_admin_gererformation',[]);
        else  return $this->redirectToRoute('app_mes_formations', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/search',name: 'searchFormationName',methods:['GET'])]
    public function searchFormationName(Request $req,ManagerRegistry $doctrine,NormalizerInterface $normalizer,FormationRepository $fr)
    {
        $searchValue = $req->get('searchValue');
        $critere = $req->get('critere');

        $formations = $fr->findformationByX($searchValue,$critere);
        if (count($formations) > 0) {
            foreach ($formations as $f) {
                $name=$doctrine->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
                $f->setNomFormateur($name);
            }
        }
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
        if (isset($user)) {
            if ($user->getIdUser() == $formation->getIdFormateur()->getIdUser()) $mine = True;
            else {
                $inscription = $doctrine->getRepository(Inscription::class)->findOneBy([
                    'idUser' => $user->getIdUser(),
                    'idFormation' => $formation->getIdFormation(),
                ]);
                if  (isset($inscription)) {
                    $inscri = True;

                    $dateinscription = $inscription->getDateInscription();
                    $now = new \DateTime();
                    $duree = $formation->getDuree() *7;
                    $diff = $now->diff($dateinscription);

                    if ($diff->days > 0) {
                        $progress = floor(($diff->days / $duree)*100);
                        if ($progress > 100) $progress = 100;
                    }
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
            'stats' => $stats

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
            $sum = round($sum / $total,2) ;

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




}


