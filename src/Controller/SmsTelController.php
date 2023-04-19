<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Twilio\Rest\Client;



class SmsTelController extends AbstractController
{
   
    // rest of the controller code
    #[Route('/app-sms', name: 'app_sms', methods: ['POST','GET'])]
    public function sendSMS(Request $request)
{
    $toPhoneNumber = $request->request->get('toPhoneNumber');
    
    $sid = 'AC365277a31739ad33daf6760f7ebeb80b';
    $token = 'fdaece6c4fb34bd8deb46a6113e9442f';
    $client = new Client($sid, $token);
    $message = $client->messages->create(
        "+216".$toPhoneNumber,
        [
            'from' => '+16205428363', 
            'body' => 'La réclamation du Client Foulen Ben Foulen a été éditée le ',
        ]
    );
  
    //$em->flush();
    return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);

}
}