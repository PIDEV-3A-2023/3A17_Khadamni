<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Candidaturestage;
use App\Entity\Emploi;
use App\Entity\Favoris;
use App\Entity\User;
use App\Form\EmploiType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FontLib\Table\Type\name;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/emploi')]
class EmploiController extends AbstractController
{
    #[Route('/', name: 'app_emploi_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $emplois = $entityManager
            ->getRepository(Emploi::class)
            ->findAll();

        return $this->render('Admin/EmploiBack.html.twig', [
            'emplois' => $emplois,
        ]);
    }


    public function isLikedByUser($idEmploi, $idUser, EntityManagerInterface $entityManager)
    {
        $favoris = $entityManager->getRepository(Favoris::class)->findOneBy([
            'idEmploi' => $idEmploi,
            'idUser' => $idUser
        ]);

        return $favoris;
    }

    #[Route('/front', name: 'app_emploi_front', methods: ['GET'])]
    public function userfront(EntityManagerInterface $entityManager): Response
    {
        $emplois = $entityManager
            ->getRepository(Emploi::class)
            ->findAll();
        $user = $this->getUser();
            foreach ($emplois as $emploi) {
                if (isset($user)) {
                    $Liked = $this->isLikedByUser($emploi->getIdEmploi(), $user->getIdUser(), $entityManager);

                    if (isset($Liked)) {
                        $emploi->setLiked(true);
                    } else  $emploi->setLiked(false);
                } else  $emploi->setLiked(false);
            }

        return $this->render('emploi/emploiF.html.twig', [
            'emplois' => $emplois,


        ]);
    }
    #[Route('/mesemploisPage', name: 'app_emploi_mesemploi')]
    public function mesEmploi(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();

        $mesemplois = $entityManager->getRepository(Emploi::class)->findBy(['idUser'=>$user->getIdUser()]);

        return $this->render('emploi/mesEmplois.html.twig', [
            'emplois' => $mesemplois,
        ]);
    }
   #[Route('/listeCandidats/{idEmploi}',name:'app_emploi_listedescandidats')]
   public function listedescandidats(EntityManagerInterface $entityManager,$idEmploi) {

       $candidatures = $entityManager->getRepository(Candidature::class)->findBy([
           'idEmploi' => $idEmploi
       ]);


       $users = [];
       foreach ($candidatures as $candidature) {
           $user = $entityManager->getRepository(User::class)->find($candidature->getIdUser()->getIdUser());
           $users[] = $user;
       }

       return $this->render('emploi/CandidatsEmploi.html.twig', [
           'candidats' => $users,
           'idEmploi' => $idEmploi
       ]);
   }

    #[Route('/new', name: 'app_emploi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $emploi = new Emploi();
        $emploi->setDatePublication(new DateTime());
        $emploi->setIdUser($user);

        $form = $this->createForm(EmploiType::class, $emploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($emploi);
            $entityManager->flush();

            return $this->redirectToRoute('app_emploi_mesemploi', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emploi/new.html.twig', [
            'emploi' => $emploi,
            'form' => $form,
        ]);
    }

    #[Route('/{idEmploi}', name: 'app_emploi_show', methods: ['GET'])]
    public function show(Emploi $emploi): Response
    {
        return $this->render('emploi/show.html.twig', [
            'emploi' => $emploi,
        ]);
    }

    #[Route('/{idEmploi}/edit', name: 'app_emploi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emploi $emploi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmploiType::class, $emploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_emploi_mesemploi', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emploi/edit.html.twig', [
            'emploi' => $emploi,
            'form' => $form,
        ]);
    }

    #[Route('/{idEmploi}', name: 'app_emploi_delete', methods: ['POST'])]
    public function delete(Request $request, Emploi $emploi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $emploi->getIdEmploi(), $request->request->get('_token'))) {
            $entityManager->remove($emploi);
            $entityManager->flush();
        }
        $ref = $request->headers->get('referer');
        if (!strpos($ref,'mesemploisPage'))  {
            return $this->redirectToRoute('app_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_emploi_mesemploi',[]);

    }

    #[Route('/detailFront/{idEmploi}', name: 'detail_emploi_front', methods: ['GET'])]
    public function showempFront(Emploi $emploi): Response
    {
        return $this->render('emploi/detailEmp.html.twig', [
            'emploi' => $emploi,
        ]);
    }

    #[Route('/like/{idEmploi}', name: 'app_emploi_like', methods: ['GET'])]
    public function showlike(Emploi $emploi): Response
    {
        return $this->render('emploi/like.html.twig', [
            'emploi' => $emploi,
        ]);
    }

    #[Route('/emploiss/{idEmploi}/favoris', name: 'emploi_favoris', methods: ['POST'])]
    public function addFavoris(Emploi $emploi, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $Liked = $this->isLikedByUser($emploi->getIdEmploi(), $user->getIdUser(), $entityManager);
        if (isset($Liked)) {
            return $this->redirectToRoute('app_favoris_index');
        }
        $favoris = new Favoris();
        $favoris->setIdEmploi($emploi);
        $favoris->setIdUser($user);

        $entityManager->persist($favoris);
        $entityManager->flush();

        return $this->redirectToRoute('app_favoris_index');
    }




}
