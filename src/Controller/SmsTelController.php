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
    $toPhoneNumber = '92499407';
    $userNumber = $request->get('tel');
    
   
    
    $sid = 'AC365277a31739ad33daf6760f7ebeb80b';
    $token = '287ab2819a7c3bb986ad699f4191c299';
    $client = new Client($sid, $token);
    $message = $client->messages->create(
        "+216".$toPhoneNumber,
        [
            'from' => '+16205428363', 
            'body' => 'Votre Reclamation a été traitée',
        ]
    );
  
    //$em->flush();
    return $this->redirectToRoute('app_reclamation_back', [], Response::HTTP_SEE_OTHER);

}
}