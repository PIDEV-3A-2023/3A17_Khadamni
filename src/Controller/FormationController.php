<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\User;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation_index')]
    public function index(EntityManagerInterface $em,ManagerRegistry $doctrine): Response
    {


        $formations = $em
            ->getRepository(Formation::class)
            ->findAll();
        foreach ($formations as $f) {
            $name=$doctrine->getRepository(Formation::class)->getNomFormateur($f->getIdFormateur());
            $f->setNomFormateur($name);
        }
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }



    #[Route('/mesformations',name:'app_mes_formations')]
    public function MesFormations(ManagerRegistry $doctrine ,Request $request) : Response
    {
        $formations = $doctrine
            ->getRepository(Formation::class)
            ->findAll();
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
            'form' => $form
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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


