<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Service\MeteoConceptService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpClient\HttpClient;

// Replace the URL with the API endpoint you need to call
$url = 'https://api.meteo-concept.com/';

// Replace the token with your own
$token = '76ef4f65db70b36e26f95440670053d2e2c3c9d8c4ac96ba9b483ec0c5ee4e5a';

// Create a new HTTP client instance
$client = HttpClient::create();

// Make a request with the Authorization header
$response = $client->request('GET', $url, [
    'headers' => [
        'Authorization' => 'Bearer ' . $token
    ]
]);

// Get the response content
$content = $response->getContent();


class EvenementFrontController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement_front')]

    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $orderBy = $request->query->get('order_by', 'date_desc');
        
        $repository = $this->getDoctrine()->getRepository(Evenement::class);
        $queryBuilder = $repository->createQueryBuilder('e');
        
        if ($orderBy === 'date_asc') {
            $queryBuilder->orderBy('e.dateevenement', 'ASC');
        } else {
            $queryBuilder->orderBy('e.dateevenement', 'DESC');
        }
        
        $evenementsPaginated = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            4 // items per page
        );
        
        $totalResults = $evenementsPaginated->getTotalItemCount();
        $currentPage = $evenementsPaginated->getCurrentPageNumber();
        $perPage = $evenementsPaginated->getItemNumberPerPage();

        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($currentPage * $perPage, $totalResults);

        $resultMessage = "Affichage $startResult-$endResult de $totalResults événement(s)";

        return $this->render('evenement/index_front.html.twig', [
            'evenements' => $evenementsPaginated,
            'resultMessage' => $resultMessage
        ]);
    }

    #[Route('/evenement/new', name: 'app_evenement_createFront', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $evenement = new Evenement();

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $inviter = $request->request->get('inviter');
            $date = $request->request->get('date');

            // Vérifier que les champs ne sont pas vides
            if (empty($nom) || empty($description) || empty($inviter) || empty($date)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->redirectToRoute('app_evenement_createFront');
            }

            // Vérifier que la date est valide
            if (!\DateTime::createFromFormat('Y-m-d', $date)) {
                $this->addFlash('error', 'La date n\'est pas valide.');
                return $this->redirectToRoute('app_evenement_createFront');
            }

            // Si tout est OK, créer l'événement
            $evenement->setNomevenement($nom);
            $evenement->setDescriptionevenement($description);
            $evenement->setInviter($inviter);
            $evenement->setDateevenement(new \DateTime($date));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
            return $this->redirectToRoute('app_evenement_front');
        }

        return $this->render('evenement/edit.html.twig');
    }
    
    /*#[Route('/evenement/{idevenement}', name: 'app_evenement_showFront', methods: ['GET'])]
    public function show(Evenement $evenement, MeteoConceptService $meteoService): Response
    {
        // Get the weather information for the city where the event is taking place
        $weather = $meteoService->getWeather($evenement->getCity());

        return $this->render('evenement/show_front.html.twig', [
            'evenement' => $evenement,
            'weather' => $weather,
        ]);
    }*/

    #[Route('/evenement/{idevenement}', name: 'app_evenement_showFront', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show_front.html.twig', [
            'evenement' => $evenement,
        ]);
    }

}

