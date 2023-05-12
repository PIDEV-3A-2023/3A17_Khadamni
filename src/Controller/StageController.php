<?php

namespace App\Controller;

use App\Entity\Prerequis;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Entity\Stage;
use App\Form\StageType;
use App\Entity\Formation;
use FontLib\Table\Type\name;
use Geocoder\Model\Bounds;
use App\Entity\Candidature;
use Doctrine\ORM\EntityManager;
use App\Entity\Candidaturestage;
use Geocoder\Query\GeocodeQuery;
use Knp\Snappy\GeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\GoogleMaps\HttpAdapter\Guzzle6HttpAdapter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/stage')]
class StageController extends AbstractController
{

    #[Route('/', name: 'app_stage_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $stages = $entityManager
            ->getRepository(Stage::class)
            ->findBy(["idUser" => $user]);

        return $this->render('stage/index.html.twig', [
            'stages' => $stages,
        ]);
    }


    #[Route('/new', name: 'app_stage_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $stage = new Stage();
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stage->setIdUser($user);
            $entityManager->persist($stage);
            $entityManager->flush();

            return $this->redirectToRoute('app_prerequis_new', ['idStage' => $stage->getIdStage()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stage/new.html.twig', [
            'stage' => $stage,
            'form' => $form,
        ]);
    }



    #[Route('/{idStage}/edit', name: 'app_stage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stage/edit.html.twig', [
            'stage' => $stage,
            'form' => $form,
        ]);
    }

    #[Route('/{idStage}', name: 'app_stage_delete', methods: ['POST'])]
    public function delete(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stage->getIdStage(), $request->request->get('_token'))) {
            $entityManager->remove($stage);
            $entityManager->flush();
        }

        $referer = $request->headers->get('referer');
        if (strpos($referer,'admin')) {
            return $this->redirectToRoute('app_admin_stagesback', [], Response::HTTP_SEE_OTHER);

        }

        return $this->redirectToRoute('app_stage_index',[]);

    }



    #[Route('/front', name: 'app_stage_front', methods: ['GET'])]
    public function indexfront(EntityManagerInterface $entityManager): Response
    {
        $stages = $entityManager
            ->getRepository(Stage::class)
            ->findAll();
        $user = $this->getUser();
        if (isset($user)) {

            $stages = array_filter($stages, function ($stage) use ($user, $entityManager) {

                return $this->filterUserStages($stage, $user, $entityManager);
            });
        }

        return $this->render('stage/stagefront.html.twig', [
            'stages' => $stages,
        ]);
    }


    #[Route('/postuler/{idStage}', name: 'app_stage_postuler')]
    public function postuler(EntityManagerInterface $entityManager, Stage $stage): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $candidature = new Candidaturestage();
        $candidature->setIdStage($stage);
        $user = $this->getUser();
        $candiat = $entityManager->getRepository(Candidaturestage::class)->findOneBy([
            'idStage' => $stage->getIdStage(),
            'idUser' => $user->getIdUser()
        ]);
        if (isset($candiat)) {
            return $this->redirectToRoute('app_candidaturestage_index');
        }


        $candidature->setIdUser($user);
        $entityManager->persist($candidature);
        $entityManager->flush();


        return $this->redirectToRoute('app_candidaturestage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/candidats/{idStage}', name: 'app_stage_candidats', methods: ['GET'])]
    public function candidats(int $idStage, EntityManagerInterface $entityManager): Response
    {


        // Récupérer la liste des candidatures par ID de stage
        $candidatures = $entityManager->getRepository(Candidaturestage::class)->findBy(['idStage' => $idStage]);

        $users = [];
        foreach ($candidatures as $candidature) {
            $user = $entityManager->getRepository(User::class)->find($candidature->getIdUser()->getIdUser());
            $users[] = $user;
        }
        // Rendre la vue avec la liste des utilisateurs correspondants
        return $this->render('stage/affichercandidats.html.twig', [
            'candidats' => $users,
        ]);
    }






    #[Route('/pdf', name: 'stage_pdf')]
    public function StagePdf(EntityManagerInterface $entityManager): Response
    {
        $stages = $entityManager
            ->getRepository(Stage::class)
            ->findAll();
        $pdfOptions = [
            'margin-top' => 10,
            'margin-right' => 10,
            'margin-bottom' => 10,
            'margin-left' => 10,
            'encoding' => 'UTF-8',
            'footer-right' => '[page]',
            'footer-font-size' => 8,
        ];



        $html = $this->renderView('stage/data.html.twig', [
            'imageSrc' => $this->imageToBase64($this->getParameter('kernel.project_dir') . "/public/LOGOKHADAMNI.png"),
            'imageSrc1' => $this->imageToBase64($this->getParameter('kernel.project_dir') . "/public/bg.png"),
            //      'imageSrc2'=> $this->imageToBase64($this->getParameter('kernel.project_dir') . "/public/barcode.jpg"),

            'stages' => $stages,

        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        return new Response(
            $dompdf->stream('resume.pdf', ['Attachment' => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function imageToBase64($path)
    {
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    #[Route('/filtrer', name: 'app_stage_filterfront')]
    function filterFront(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer)
    {
        $type = $request->get('type');
        $niv = $request->get('niveau_etude');

        $user = $this->getUser();
        if ($type !== 'all')
            $stages = $entityManager->getRepository(Stage::class)->findBy([
                'typeStage' => $type
            ]);
        else $stages = $entityManager->getRepository(Stage::class)->findAll();
        $FiltredStages = [];

        if (isset($user)) {
            $stages = array_filter($stages, function ($stage) use ($user, $entityManager) {

                return $this->filterUserStages($stage, $user, $entityManager);
            });
        }


        if (isset($niv)) {
            foreach ($stages as $stage) {
                $prerequis = $entityManager->getRepository(Prerequis::class)->findOneBy(['idStage' => $stage->getIdStage()]);
                if (isset($prerequis)) {
                    $niveau = $prerequis->getNiveauEtude();
                    if (in_array($niveau, $niv))
                        $FiltredStages[] = $stage;
                }

            }
        } else $FiltredStages = $stages;


        $StagesJSON = $serializer->serialize($FiltredStages, 'json');

        return new Response($StagesJSON);
    }

    function filterUserStages($stage, $user, EntityManagerInterface $entityManager)
    {

        $candidature = $entityManager->getRepository(Candidaturestage::class)->findOneBy([
            'idStage' => $stage->getIdStage(),
            'idUser' =>  $user->getIdUser(),
        ]);

        return (!isset($candidature) && $stage->getIdUser() !== $user->getIdUser());
    }


    #[Route('/candidature', name: 'app_candidaturestage_index', methods: ['GET'])]
    public function can(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $candidatures = $entityManager
            ->getRepository(Candidaturestage::class)
            ->findBy(["idUser" => $user]);
        return $this->render('stage/cand.html.twig', [
            'candidatures' => $candidatures,

        ]);
    }
}
