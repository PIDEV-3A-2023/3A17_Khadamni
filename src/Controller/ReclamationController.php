<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\SuiviReclamation;
use App\Entity\User;
use App\Form\SuiviReclamationType;
use Symfony\Component\Mime\Message;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\EtatReclamationType;

use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{


    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,PaginatorInterface $paginator,Request $request): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

            foreach ($reclamations as $reclamation)
            {
                    $suivi =$entityManager->getRepository(SuiviReclamation::class)->findOneBy(['idReclamation'=> $reclamation->getIdReclamation()]);
                    if (isset($suivi))
                    $reclamation->setEtat($suivi->getEtatReclamation());
                    else $reclamation->setEtat('non_traitee');

            }

            $pagination =   $paginator->paginate(
                $reclamations,
                $request->query->getInt('page', 1), 5
            );
        return $this->render('reclamation/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }



    #[Route('/stat', name: 'app_reclamation_stat')]
public function stat(ChartBuilderInterface $chartBuilder, EntityManagerInterface $entityManager): Response
{
    $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
    $chart2 = $chartBuilder->createChart(Chart::TYPE_PIE);
   
    $x=0;
    $y=0;
    $z=0;


$suiviReclamation = $entityManager
    ->getRepository(SuiviReclamation::class)
    ->findAll();


    
for($i=0;$i<count($suiviReclamation);$i++){
    if($suiviReclamation[$i]->getEtatReclamation()=="en_cours"){
        $x+=1;
    }else 
    if($suiviReclamation[$i]->getEtatReclamation()=="non traitee"){
        $y+=1;
}else {$z+=1;} 
}

    $chart->setData([
        'labels' => ['En Cours', 'non Traité', 'Traité'],
        'datasets' => [
            [
                'label' => 'Réclamations',
                'backgroundColor' => ['#007bff', '#28a745', '#dc3545'],
                'borderColor' => ['#007bff', '#28a745', '#dc3545'],
                'data' => [$x,$y,$z],
            ],
        ],
    ]);

    $chart->setOptions([
        'scales' => [
            'y' => [
                'suggestedMin' => 0,
                'suggestedMax' => 100,
            ],
        ],
    ]);


    $reclamations = $entityManager->getRepository(Reclamation::class)->findAll();

// Initialize counts for each year to zero
$reclamations2021 = 0;
$reclamations2022 = 0;
$reclamations2023 = 0;

// Loop through all reclamations and count the ones for each year
foreach ($reclamations as $reclamation) {
    $year = $reclamation->getDateReclamation()->format('Y');

    if ($year == '2021') {
        $reclamations2021++;
    } elseif ($year == '2022') {
        $reclamations2022++;
    } elseif ($year == '2023') {
        $reclamations2023++;
    }
}

// Set the chart data using the counts for each year
$chart2->setData([
    'labels' => ['2021', '2022', '2023'],
    'datasets' => [
        [
            'label' => 'Réclamations',
            'backgroundColor' => ['#007bff', '#28a745', '#dc3545'],
            'borderColor' => ['#007bff', '#28a745', '#dc3545'],
            'data' => [$reclamations2021, $reclamations2022, $reclamations2023],
        ],
    ],
]);

$chart2->setOptions([
    'scales' => [
        'y' => [
            'suggestedMin' => 0,
            'suggestedMax' => 100,
        ],
    ],
]);

return $this->render('reclamation/stat.html.twig', [
    'chart' => $chart,
    'chart2' =>$chart2

]);
}

   #[Route('/modifier/{id}', name: 'app_reclamation_etat')]
    public function modifier_etat(EntityManagerInterface $entityManager,Reclamation $reclamation): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

  #[Route('/front', name: 'app_reclamation_front', methods: ['GET'])]
    public function front(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findBy(['idUser' => $user->getIdUser()]);
        return $this->render('reclamation/front.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    #[Route('/ajouterRec', name: 'app_reclamation_ajouterfromhome', methods: ['GET', 'POST'])]
    public function ajouterFromHome(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();


        $date = new DateTime();
        $formattedDate = $date->format('Y-m-d');
        $reclamation->setDateReclamation(new \DateTime($formattedDate));


        $badWordsFile = __DIR__ . '/../../mots-interdites.txt';

        $badWords = file($badWordsFile, FILE_IGNORE_NEW_LINES);
        $pattern = '/\b(' . implode('|', $badWords) . ')\b/i';


        // Detect bad words in the description
        $description = $request->get('description');
        if (preg_match($pattern, $description)) {
            // Add a flash message to inform the user that the description contains bad words
            $this->addFlash('warning', 'Votre description contient des mots interdits.');
        } else {
            $email = $request->get('email');
            $reclamation->setEmail($email);
            $reclamation->setDescription($description);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $suivi = new SuiviReclamation();
            $suivi->setEtatReclamation("non traitee");
            $suivi->setIdReclamation($reclamation);
            $entityManager->persist($suivi);
            $entityManager->flush();

        }

        $this->addFlash('info', 'Nous avons bien reçu votre réclamation..');
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);

    }

  #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $reclamation = new Reclamation();
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    $user = $this->getUser();
    // Set the default value for the dateReclamation field
    $date = new DateTime();
    $formattedDate = $date->format('Y-m-d');
    $reclamation->setDateReclamation(new \DateTime($formattedDate));

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    $badWordsFile = __DIR__ . '/../../mots-interdites.txt';

    $badWords = file($badWordsFile, FILE_IGNORE_NEW_LINES);
    $pattern = '/\b(' . implode('|', $badWords) . ')\b/i';
    if ($form->isSubmitted() && $form->isValid()) {
        $reclamation->setIdUser($user);

        // Detect bad words in the description
        $description = $form->get('description')->getData();
        if (preg_match($pattern, $description)) {
            // Add a flash message to inform the user that the description contains bad words
            $this->addFlash('warning', 'Votre description contient des mots interdits.');
        } else {
            // Save the reclamation and create a new SuiviReclamation
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $suivi = new SuiviReclamation();
            $suivi->setEtatReclamation("non traitee");
            $suivi->setIdReclamation($reclamation);
            $entityManager->persist($suivi);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_reclamation_front', [], Response::HTTP_SEE_OTHER);
        }
    }

    return $this->renderForm('reclamation/new.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form,
    ]);
}

 #[Route('/{idReclamation}', name: 'app_reclamation_show')]
    public function show(Reclamation $reclamation,EntityManagerInterface $entityManager,Request $request): Response
    {
        
        

        $suiviReclamation = $entityManager
        ->getRepository(SuiviReclamation::class)
        ->findOneBy(['idReclamation' => $reclamation->getIdReclamation()]);

        $suivi = new SuiviReclamation() ; 
                $form = $this->createForm(EtatReclamationType::class,$suivi);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $suivi = $entityManager->getRepository(SuiviReclamation::class)->findOneBy(['idReclamation' => $reclamation->getIdReclamation()]);

        
            if ($suivi) {
                $suivi->setEtatReclamation($form->get("etatReclamation")->getData());
                $suivi->setSujet($form->get("sujet")->getData());
                $suivi->setMotif($form->get("motif")->getData());
                
                $entityManager->flush();
                if ($suivi->getEtatReclamation()== "traitee") {
                   
                    $user = $entityManager->getRepository(User::class)->find($reclamation->getIdUser()->getIdUser());
                    return $this->redirectToRoute('app_sms', ['tel' => $user->getNumTel()]);
                   
                } 
                
            } 
        
            return $this->redirectToRoute('app_reclamation_back', [], Response::HTTP_SEE_OTHER);
        }
         return $this->renderForm('admin/EditRec.html.twig', [
            'reclamation' => $reclamation,
            'form'=>$form , 
            'suiviReclamation'=>$suiviReclamation,
            

        ]);
    }

    #[Route('/{idReclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $dateCreation = $reclamation->getDateReclamation();
            $dateActuelle = new \DateTime();
    
            $interval = $dateActuelle->diff($dateCreation);
            $jours = $interval->days;
    
            if ($jours <= 1) { 
                $entityManager->flush();
                return $this->redirectToRoute('app_reclamation_front', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('warning', 'La modification de la réclamation est interdite après 24 heures ');
            }
        }
    
        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/del/{idReclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdReclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_back', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('reclamation/repondre/{idReclamation}', name: 'app_reclamation_repondre', methods: ['GET', 'POST'])]
    public function repondre(Request $request, EntityManagerInterface $entityManager, $idReclamation)
    {
        $suiviReclamation = new SuiviReclamation();
        $form = $this->createForm(SuiviReclamationType::class, $suiviReclamation);
        
        $reclamation = $entityManager
            ->getRepository(Reclamation::class)
            ->find($idReclamation);
        
        $suiviReclamation = $entityManager
            ->getRepository(SuiviReclamation::class)
            ->findOneBy([
                'idReclamation' => $idReclamation
            ]);
        
        if (!$suiviReclamation) {
            $suiviReclamation = new SuiviReclamation();
            $suiviReclamation->setIdReclamation($reclamation);
            $entityManager->persist($suiviReclamation);
        }
        
        $form = $this->createForm(SuiviReclamationType::class, $suiviReclamation);
    
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
        
   
        
            $entityManager->persist($suiviReclamation);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_reclamation_front', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('reclamation/repondre.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }



/**
 * @Route("/reclamation/messages/{idReclamation}", name="app_reclamation_messages")
 */
public function messages(Request $request, $idReclamation)
{
    $reclamation = $this->getDoctrine()
        ->getRepository(Reclamation::class)
        ->find($idReclamation);

    $suiviReclamation = $this->getDoctrine()
        ->getRepository(SuiviReclamation::class)
        ->findOneBy([
            'idReclamation' => $idReclamation
        ]);

    $form = $this->createFormBuilder()
        ->add('avis', TextareaType::class, [
            'label' => 'Avis'
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Save'
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();

        $suiviReclamation->setAvis($data['avis']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($suiviReclamation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reclamation_front');
    }

    return $this->render('reclamation/messages.html.twig', [
        'reclamation' => $reclamation,
        'suiviReclamation' => $suiviReclamation,
        'form' => $form->createView()
    ]);
}

#[Route('/back/ok', name: 'app_reclamation_back', methods: ['GET'])]
public function back(EntityManagerInterface $entityManager): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $reclamations = $entityManager
        ->getRepository(Reclamation::class)
        ->findAll();
    foreach ($reclamations as $reclamation)
    {
        $suivi =$entityManager->getRepository(SuiviReclamation::class)->findOneBy(['idReclamation'=> $reclamation->getIdReclamation()]);
        if (isset($suivi))
            $reclamation->setEtat($suivi->getEtatReclamation());
        else $reclamation->setEtat('non_traitee');

    }

    return $this->render('Admin/RaclamtionBack.html.twig', [
        'reclamations' => $reclamations,
    ]);
}






}