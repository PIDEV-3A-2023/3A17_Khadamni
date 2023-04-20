<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(MailerInterface $mailer,Request $request,EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $session = $request->getSession();
        $IDformation = $session->get('formationID');
        $formation = $entityManager->getRepository(Formation::class)->find($IDformation);
        $name=$entityManager->getRepository(Formation::class)->getNomFormateur($formation->getIdFormateur());
        $formation->setNomFormateur($name);
        $email = (new TemplatedEmail())
            ->from('khadamni12@gmail.com')
            ->to('atefbadreddine05@gmail.com')
            ->subject('Facture de Paiement Khadamni')
            ->htmlTemplate('formation/FactureMail.html.twig')
            ->context([
                'formation' => $formation,
                'user' => $user,
                'date' => new \DateTime(),
            ]);

        try {
            $mailer->send($email);
            $session->getFlashBag()->add('info', 'Félicitations, votre paiement a été effectué avec succès ! Vous pouvez maintenant commencer à suivre la formation.');
            return $this->redirectToRoute('app_formation_index', [
            ]);
        } catch (TransportExceptionInterface $e) {
            $session->getFlashBag()->add('error', 'Erreur');
        }

        return $this->redirectToRoute('app_formation_index', [
        ]);
    }
}






