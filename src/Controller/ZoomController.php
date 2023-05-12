<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

class ZoomController extends AbstractController
{
    #[Route('/zoom/{idEmploi}/{idUser}', name: 'app_zoom', methods: ['GET', 'POST'])]
    public function index(Request $request,$idEmploi,$idUser,MailerInterface $mailer,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $candidat = $entityManager->getRepository(User::class)->find($idUser);
        $emploi = $entityManager->getRepository(Emploi::class)->find($idEmploi);
        $duration = $request->get('duration');
        $date = $request->get('date');
        $heure = $request->get('heure');

        $startUrl = '';
        $joinUrl = '';
        $mdp = '';
        $session= $request->getSession();


        if (isset($date) && isset($heure) && isset($duration)) {

            $zoom = $this->generateZoom();

            $startUrl = $zoom[0];
            $joinUrl = $zoom[1];
            $mdp = $zoom[2];
            $email = (new TemplatedEmail())
                ->from('khadamni12@gmail.com')
                //->to($user->getEmail())
                ->to('atefbadreddine05@gmail.com')
                ->subject('Entretien')
                ->htmlTemplate('emploi/zoomMail.html.twig')
                ->context([
                    'user' => $candidat,
                    'mdp' => $mdp,
                    'joinUrl' => $joinUrl,
                    'startUrl' => $startUrl,
                    'duration' => $duration,
                    'date' => $date,
                    'heure'=> $heure,
                    'emploi' => $emploi
                ]);

            try {
                $mailer->send($email);
                $session->getFlashBag()->add('info', 'Un mail a été envoyé vers '.$candidat->getEmail());


            } catch (TransportExceptionInterface $e) {
                $session->getFlashBag()->add('error', 'Erreur');
            }
        }





        return $this->render('candidature/zoom.html.twig', [
            'startUrl' =>  $startUrl,
            'joinUrl' => $joinUrl,
            'mdp' => $mdp,
            'idEmploi' => $idEmploi,
            'idUser' => $idUser,

        ]);
    }
    function jwtGenerator($publicKey, $secretKey)
    {
        $payload = [
            'iss' => $publicKey,
            'exp' => time() + 60
        ];
        $jwtToken = JWT::encode($payload, $secretKey, 'HS256');
        return $jwtToken;
    }
    function generateZoom()
    {

        $client = new Client([
            'base_uri' => 'https://api.zoom.us/v2/',
            'headers' => [
                'Authorization' => 'Bearer' . $this->jwtGenerator($_ENV['ZOOM_APIKEY'], $_ENV['ZOOM_SECRETKEY']),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]

        ]);

        try {
            $response = $client->get('users');
            $body = $response->getBody();
            $results = json_decode($body, true);
            $userId = $results['users'][0]['id'];

            //creer le meeting

            $createMeet = $client->post('users/' . $userId . '/meetings', [
                'json' => [
                    'type' => 1,
                    'timezone' => 'Africa/Casablanca'
                ]
            ]);

            //info meet
            $meet_infos = $createMeet->getBody();
            $meet_info = json_decode($meet_infos, true);

            $startUrl = $meet_info['start_url'];
            $joinUrl = $meet_info['join_url'];
            $mdp = $meet_info['password'];
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
        return [$startUrl, $joinUrl, $mdp];
    }
}
