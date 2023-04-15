<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\User;
use App\Form\FormationType;
use App\Repository\FormationRepository;
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
            $name=$doctrine->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
            $f->setNomFormateur($name);
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
        $success_url = $this->generateUrl('app_success', ['from'=>'checkout'],UrlGeneratorInterface::ABSOLUTE_URL);

        // Set session variable
        $session = $request->getSession();
        $session->set('formation', $formation);


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
            $id = $session->get('checkout_session_id');

            $formation = $session->get('formation');

            $nf = $entityManager->getRepository(Formation::class)->find($formation->getIdFormation());
            $inscri = new Inscription();
            $inscri->setIdFormation($nf);
            $inscri->setIdUser($user);
            $inscri->setDateInscription(new \DateTime());


            $entityManager->persist($inscri);
            $entityManager->flush();
            $session->getFlashBag()->add('info', 'Félicitations, votre paiement a été effectué avec succès ! Vous pouvez maintenant commencer à suivre la formation.');
            return $this->redirectToRoute('app_formation_index',[

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
        if (count($formations) > 0)
            foreach ($formations as $f) {
                $name=$entityManager->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
                $f->setNomFormateur($name);
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
        $stripe = new StripeService();
        $hours = $today->diff($inscription->getDateInscription())->h;
        if ($hours < 48 )
        $stripe->refundMoney($user,$formation,$formateur,$formation->getPrix()*100);

            $entityManager->remove($inscription);
            $entityManager->flush();


        return $this->redirectToRoute('app_inscrit');

    }
    #[Route('/{idFormation}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
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
                    $customer = $inscription->getIdUser();
                    $date = $inscription->getDateInscription();
                    $today = new \DateTime();
                    $diff = $today->diff($date);
                    $duree = $formation->getDuree();
                    $i = $entityManager->getRepository(Inscription::class)->find($inscription->getIdInscription());
                    // Check if formation has ended
                    $endDate = clone $date;
                    $endDate->add(new \DateInterval('P' . $duree . 'W')); // Add duration in weeks
                    if ($today > $endDate) {
                        $entityManager->remove($i);
                        $entityManager->flush();
                        continue; // Skip refund if formation has ended
                    }

                    // Calculate the percentage of refund based on time passed
                    $percentageRefund = 0;
                    $weeksPassed = floor($diff->days / 7); // Calculate number of weeks passed
                    if ($weeksPassed > 0) {
                        $percentageRefund = ($weeksPassed / $duree) *100; // 50% refund for each week passed
                    }

                    // Perform refund if applicable
                    if ($percentageRefund > 0) {
                        $refundAmount = ($formation->getPrix() * $percentageRefund) / 100;
                        $stripe->refundMoney($customer,$formation,$formateur,$refundAmount);
                    }
                    $entityManager->remove($i);
                    $entityManager->flush();

                }
            }

            $entityManager->remove($formation);
            $entityManager->flush();
        }
        else {
            if ( strpos($ref,'mesformations'))
            return $this->redirectToRoute('app_mes_formations',[]);
            else return $this->redirectToRoute('app_admin_gererformation',[]);
        }
        if ( strpos($ref,'mesformations'))
            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        else return $this->redirectToRoute('app_admin_gererformation',[]);

    }

    #[Route('/search',name: 'searchFormationName',methods:['GET'])]
    public function searchFormationName(Request $req,ManagerRegistry $doctrine,NormalizerInterface $normalizer,FormationRepository $fr)
    {
        $searchValue = $req->get('searchValue');
        $critere = $req->get('critere');
        if (preg_match('/(?<=\S) {2,}(?=\S)/', $searchValue)) {
            $formations = [];
        }
        else if ($critere == 'nomFormateur') {
            $nomPrenom = explode(' ',$searchValue,2);
            if (!isset($nomPrenom[1])) $nomPrenom[1]='';
            $formations= $fr->findformationByNomFormateur($nomPrenom[0],$nomPrenom[1]);
        }
        else $formations = $fr->findformationByX($searchValue,$critere);
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
}


