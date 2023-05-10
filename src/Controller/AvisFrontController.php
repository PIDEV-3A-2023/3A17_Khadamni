<?php

namespace App\Controller;


use App\Entity\Avis;
use App\Entity\Evenement;
use App\Entity\User;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;


class AvisFrontController extends AbstractController
{   

    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }



    #[Route('/avis/ajouter/{idEvenement}', name: 'app_ajouter_avis')]
    public function createNewAvis(Request $request,$idEvenement,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $comment = $request->request->get('comment');
        $evenement = $entityManager->getRepository(Evenement::class)->find($idEvenement);
        $user = $this->getUser();
        $date = new \DateTime();
        $avis = new Avis();
        $avis->setCommentaire($comment);
        $avis->setNote(10);
        $avis->setDateCreation($date);
        $avis->setIdEvenement($evenement);
        $avis->setIdUtilisateur($user);

        $LesAvis = $entityManager
            ->getRepository(Avis::class)
            ->findAll();

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->redirectToRoute('app_evenement_showFront',[
            'idevenement' => $idEvenement,
            'avis' => $LesAvis
        ]);

    }

}
