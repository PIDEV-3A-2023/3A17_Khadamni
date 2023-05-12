<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Emploi;
use App\Entity\User;
use App\Form\CandidatureType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/candidature')]
class CandidatureController extends AbstractController
{
    #[Route('/', name: 'app_candidature_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $candidatures = $entityManager
            ->getRepository(Candidature::class)
            ->findBy(["idUser" => $user]);
        $chance =  $this->generateEqualityReport($entityManager);
        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
            'report' => $chance
        ]);
    }

    #[Route('/new/{IdEmploi}', name: 'app_candidature_new')]
    public function new(Request $request, $IdEmploi, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $emploi = $entityManager->getRepository(Emploi::class)->find($IdEmploi);

        $candiat = $entityManager->getRepository(Candidature::class)->findOneBy([
            'idEmploi' => $IdEmploi,
            'idUser' => $user->getIdUser()
        ]);
        if (isset($candiat)) {
            return $this->redirectToRoute('app_candidature_index');
        }
        $candidature = new Candidature();
        $candidature->setDate(new DateTime());
        $candidature->setIdUser($user);
        $candidature->setIdEmploi($emploi);
        $entityManager->persist($candidature);
        $entityManager->flush();

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idCandidature}', name: 'app_candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{idCandidature}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{idCandidature}', name: 'app_candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidature->getIdCandidature(), $request->request->get('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }

    public function generateEqualityReport(EntityManagerInterface $entityManager)
    {


        $candidatures = $entityManager
            ->getRepository(User::class)
            ->findAll();
        $totalCandidates = count($candidatures);

        $totalUnder10 = 0;
        $totalBetween10And15 = 0;
        $totalOver20 = 0;

        foreach ($candidatures as $candidature) {
            $note = $candidature->getNote();
            if ($note < 10) {
                $totalUnder10++;
            } else if ($note >= 10 && $note <= 15) {
                $totalBetween10And15++;
            } else {
                $totalOver20++;
            }
        }

        $percentageUnder25 = ($totalUnder10 / $totalCandidates) * 100;
        $percentageBetween25And50 = ($totalBetween10And15 / $totalCandidates) * 100;
        $percentageOver50 = ($totalOver20 / $totalCandidates) * 100;

        // Génération du rapport
        $report =
             "Nombre total de candidatures: " . $totalCandidates . "\n"
            . "Nombre de Notes Moins de 10: " . $totalUnder10 . " (" . round($percentageUnder25, 2) . "%)\n"
            . "Nombre de Notes entre 10 et 15: " . $totalBetween10And15 . " (" . round($percentageBetween25And50, 2) . "%)\n"
            . "Nombre de Notes égales à  20: " . $totalOver20 . " (" . round($percentageOver50, 2) . "%)\n";

        $formattedReport = nl2br($report);
        // Affichage du rapport dans une vue
        return $formattedReport;
    }
}
