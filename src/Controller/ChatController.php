<?php

namespace App\Controller;

use App\Service\GoogleService;
use Exception;
use JsonException;
use OpenAI;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ChatController extends AbstractController
{
    private Google_Client $client;
    public function __construct(
        private readonly GoogleService   $calendarService,
        private readonly LoggerInterface $chatLogger,
        Google_Client $client
    ) {
        $this->client = $client;
    }

    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig');
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Route('/chat/message', name: 'app_chat_message', methods: ['POST'])]
    public function message(Request $request): Response
    {

        $history = json_decode($request->get('history'), true, 512, JSON_THROW_ON_ERROR);
        $client = OpenAI::client($_ENV['OPENAI_API_KEY']);

        $openAiResponse = $client->chat()->create([

            'model'    => 'gpt-3.5-turbo',
            'messages' => array_merge(
                [
                    [
                        'role'    => 'system',

                        'content' => $this->getSystemContent(),
                    ]
                ],
                $history
            ),

        ]);

        $answer = $openAiResponse->choices[0]->message->content;

        if ($meeting = $this->calendarService->processMessage($answer)) {
            // the $meeting variable contains the link to the meeting,
            // or in case of error, the error message
            $answer .= PHP_EOL . $meeting;
        }

        $this->chatLogger->info('history', ['user' => $history[count($history) - 1]['content'], 'agent' => $answer]);

        return $this->json([
            'answer' => $answer
        ]);
    }


    private function getSystemContent(): string
    {
        $content = file_get_contents(__DIR__ . '/../../system_message.txt');

        return str_replace(
            [
                '__CHAT_BOT_NAME__',
                '__COMPANY__',
                '__SERVICES__',
                '__SUPPORT_EMAIL__',
                '__AVAILABILITY__',
                '__LOCATION__',
                '__NOW_DATE_TIME__',
            ],
            [
                $_ENV['GPT_SYS_MESSAGE_CHAT_BOT_NAME'],
                $_ENV['GPT_SYS_MESSAGE_COMPANY'],
                $_ENV['GPT_SYS_MESSAGE_SERVICES'],
                $_ENV['GPT_SYS_MESSAGE_SUPPORT_EMAIL'],
                $_ENV['GPT_SYS_MESSAGE_AVAILABILITY'],
                $_ENV['GPT_SYS_MESSAGE_LOCATION'],
                date('l, F j, Y, g:i a')
            ],
            $content
        );
    }

    /**
     * Prepares the client with the user access token
     *
     * Gets the credentials from the .json file
     *
     * @return ?Google_Client
     * @throws Exception
     */
    public function loadClientCredentials(): ?Google_Client
    {

        $credentials = json_decode(file_get_contents(__DIR__ . '/../../' . 'credentials.json'), true, 512, JSON_THROW_ON_ERROR);





        $this->client->setAccessToken($credentials);

        return $this->client;
    }


    #[Route('/google/calendar', name: 'google_calendar')]
    public function request(Google_Client $client): Response
    {
        // Authenticate the client using the client ID and secret
        $client->setClientId('494911486130-rs5j70t5h9g4i52rb4nlc360uavojcjp.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri('app_home');
        $client->setDeveloperKey('AIzaSyB1QlTTUZfXSol-TRMikmcqmxH6cY6rYKk');

        // Set up the Google Calendar service
        $service = new Google_Service_Calendar($client);

        // Get the user's calendar events
        $events = $service->events->listEvents('primary');

        // Render the events in a Twig template
        return $this->render('google/calendar.html.twig', [
            'events' => $events,
        ]);
    }






    //     public function sendMeetInvitations(Request $request, \Swift_Mailer $mailer)
    // {
    //     $customerEmails = $request->request->get('customer_emails');
    //     $customerEmails = explode(',', $customerEmails);

    //     $client = new Google_Client();
    //     $client->setApplicationName('Your Application Name');
    //     $client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);
    //     $client->setAuthConfig('path/to/credentials.json');

    //     $calendar = new Google_Service_Calendar($client);

    //     foreach ($customerEmails as $customerEmail) {
    //         $event = new Google_Service_Calendar_Event(array(
    //             'summary' => 'Meeting with Customer',
    //             'description' => 'Discuss project details',
    //             'start' => array(
    //                 'dateTime' => '2023-04-25T10:00:00-07:00',
    //                 'timeZone' => 'America/Los_Angeles',
    //             ),
    //             'end' => array(
    //                 'dateTime' => '2023-04-25T11:00:00-07:00',
    //                 'timeZone' => 'America/Los_Angeles',
    //             ),
    //             'conferenceData' => array(
    //                 'createRequest' => array(
    //                     'conferenceSolutionKey' => array(
    //                         'type' => 'hangoutsMeet',
    //                     ),
    //                     'requestId' => uniqid(),
    //                 ),
    //             ),
    //             'attendees' => array(
    //                 array('email' => $customerEmail),
    //             ),
    //         ));

    //         $calendar->events->insert('primary', $event, array(
    //             'conferenceDataVersion' => 1,
    //             'sendNotifications' => true,
    //         ));

    //         // Send a confirmation email to the customer
    //         $message = (new \Swift_Message('Meeting Invitation'))
    //             ->setFrom('your_email@example.com')
    //             ->setTo($customerEmail)
    //             ->setBody(
    //                 'You have been invited to a meeting. Please check your Google Calendar for details.',
    //                 'text/plain'
    //             );

    //         $mailer->send($message);
    //     }

    //     return $this->json([
    //         'message' => 'Invitations sent!',
    //     ]);
    // }
}
