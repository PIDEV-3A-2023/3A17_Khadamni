<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZoomController extends AbstractController
{
    #[Route('/zoom', name: 'app_zoom', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $topic = $request->get('topic');
        $duration = $request->get('duration');

        $startUrl = '';
        $joinUrl = '';
        $mdp = '';

        if (isset($topic) && isset($duration)) {
            $zoom = $this->generateZoom($topic, $duration);
            $startUrl = $zoom[0];
            $joinUrl = $zoom[1];
            $mdp = $zoom[2];
        }





        return $this->render('candidature/zoom.html.twig', [
            'startUrl' =>  $startUrl,
            'joinUrl' => $joinUrl,
            'mdp' => $mdp


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
    function generateZoom($topic, $duration)
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
                    'topic' => $topic,
                    'type' => 1,
                    'duration' => $duration,
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
