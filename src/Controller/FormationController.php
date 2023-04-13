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
      // $stripe->testStripe();
        $acc=$stripe->retriveAccount($user->getEmail());

        $ref = $request->query->get('from');


        if ($ref == 'accountlink') {

           $encours = True;
        }
        else $encours = False;
        if (! ($acc->details_submitted && $acc->payouts_enabled)) {

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
        return new RedirectResponse($checkout->url);


    }

    #[Route('/success',name: 'app_success')]
    public function onPaiementSuccess(Request $request,EntityManagerInterface $entityManager)  {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $ref = $request->query->get('from');
        $user = $this->getUser();
        if ($ref == 'checkout') {
            $session = $request->getSession();
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
        if ($this->isCsrfTokenValid('delete'.$formation->getIdFormation(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }
        else {

            return $this->redirectToRoute('app_mes_formations',[]);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
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


