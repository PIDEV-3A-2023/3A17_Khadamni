<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Twilio\Rest\Client;

class SmsTelController extends AbstractController
{
    private $twilio;

    public function __construct(Client $twilio)
    {
        $this->twilio = $twilio;
    }

    // rest of the controller code

    public function sendSMS(Request $request)
{
    $toPhoneNumber = $request->request->get('toPhoneNumber');
    if (empty($toPhoneNumber)) {
        $this->addFlash('error', 'Please enter a phone number.');
        return $this->redirectToRoute('app_home');
    }

    $currentDate = new DateTime();
    $messageText = 'La réclamation du Client Foulen Ben Foulen a été éditée le ' . $currentDate->format('d/m/Y');

    try {
        $message = $this->twilio->messages->create($toPhoneNumber, [
            'from' => getenv('TWILIO_NUMBER'),
            'body' => $messageText,
        ]);

        $this->addFlash('success', 'SMS sent successfully to ' . $toPhoneNumber . '!');
    } catch (Exception $e) {
        $this->addFlash('error', 'Error sending SMS to ' . $toPhoneNumber . '.');
    }

    return $this->redirectToRoute('app_home');
}

}
