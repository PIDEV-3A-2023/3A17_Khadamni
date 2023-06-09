<?php

namespace App\Service;

use Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Contains all the logic to interact with the Google Login and the Calendar API
 */
class GoogleService
{
	private string $redirectUri;

	/**
	 * @throws \Google\Exception
	 */
	public function __construct(
		private readonly Google_Client $client,
		private readonly LoggerInterface $calendarLogger,
		UrlGeneratorInterface $urlGenerator,
	) {
		$this->client->useApplicationDefaultCredentials(false);
		$this->client->setAuthConfig(__DIR__ . '/../../' . 'client_secret.json');
		$this->client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
		$this->client->addScope(['email', 'profile']);

		// This is needed to get the refresh token
		$this->client->setAccessType('offline');

		// this need to be the same as the one in the google console Allowed redirect URIs in Services & APIs -> Credentials -> OAuth 2.0 client IDs
		$this->redirectUri = $urlGenerator->generate('google_calendar', [], UrlGeneratorInterface::ABSOLUTE_URL);
	}

	/**
	 * The url to redirect the user to in order to login
	 */
	public function getAuthUrl(): string
	{
		$this->client->setRedirectUri($this->redirectUri);
		return $this->client->createAuthUrl();
	}

	/**
	 * From A given string it will try to extract the meeting details.
	 * and if it finds it and all goes well it will
	 * Create a new event in the calendar and return the link to the event in the calendar
	 * Otherwise it will return null if the string doesn't match the expected format
	 * Or the error message if something went wrong
	 *
	 * @throws Exception
	 */
	public function processMessage(string $message): ?string
	{
		$eventParams = $this->checkForMeeting($message);
		if ($eventParams) {
			$this->calendarLogger->info('Creating event in the calendar', $eventParams);
			try {
				$eventLink = "";
				$this->calendarLogger->info('Event created', ['link' => $eventLink]);
				return $eventLink;
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		return null;
	}

	/**
	 * @param array $eventParams array{summary: string, description: string, start: array{dateTime: string, timeZone: string}, end:
	 *                           array{dateTime: string, timeZone: string}, attendees: array{0: array{email: string}}, location: string}
	 *
	 * If all goes well it will return the link to the event in the calendar
	 *
	 * @throws Exception
	 */
	public function request(array $eventParams): ?string
	{
		$client = $this->loadClientCredentials();

		$service = new Google_Service_Calendar($client);

		$event = new Google_Service_Calendar_Event($eventParams);

		$event = $service->events->insert($_ENV['GOOGLE_CALENDAR_ID'], $event);

		return $event->htmlLink;
	}


	/**
	 * Prepares the client with the user access token
	 *
	 * Gets the credentials from the .json file
	 *
	 * @return ?Client
	 * @throws Exception
	 */
	public function loadClientCredentials(): ?Google_Client
	{

		$credentials = json_decode(file_get_contents(__DIR__ . '/../../' . $_ENV['CREDENTIALS_FILE']), true, 512, JSON_THROW_ON_ERROR);


		if (!$credentials) {
			throw new RuntimeException('No user with the role ROLE_CALENDAR_OWNER, you need to create one' . PHP_EOL .
				'in the .env activate the ALLOW_CALENDAR_OWNER_LOGIN and navigate to /connect/google and login');
		}


		$this->client->setAccessToken($credentials);

		return $this->client;
	}

	/**
	
	 *
	 * @param string $answer
	 *
	 * @return array{summary: string, description: string, start: array{dateTime: string, timeZone: string}, end: array{dateTime: string,
	 *                        timeZone: string}, attendees: array{0: array{email: string}}, location: string}|null
	 * @throws Exception
	 */
	public function checkForMeeting(string $answer): ?array
	{
		$pattern = '/Email: (?<email>.*)\n*Subject: (?<subject>.*)\n*Location: (?<location>.*)\n*Date: (?<date>.*)\n*Time: (?<time>.*)\n*Duration: (?<duration>.*)/m';

		if (preg_match($pattern, $answer, $matches, PREG_OFFSET_CAPTURE)) {
			$start = new \DateTime($matches['date'][0] . ' ' . $matches['time'][0]);
			$end   = clone $start;
			$end->modify('+' . $matches['duration'][0]);

			return [
				'summary'     => $matches['subject'][0],
				'description' => $matches['subject'][0],
				'start'       => [
					'dateTime' => $start->format('Y-m-d\TH:i:sP'),
					'timeZone' => 'UTC'
				],
				'end'         => [
					'dateTime' => $end->format('Y-m-d\TH:i:sP'),
				],
				'attendees'   => [
					[
						'email' => $matches['email']
					]
				],
			];
		}

		return null;
	}


	public function saveAuthFromCode(string $code): void
	{
		$this->client->setRedirectUri($this->redirectUri);
		$this->client->fetchAccessTokenWithAuthCode($code);

		/**
		 * @var array{access_token: string, expires_in: int, refresh_token: string, scope: string, token_type: string, created: int} $token
		 */
		$token = $this->client->getAccessToken();


		file_put_contents(__DIR__ . '/../../' . $_ENV['CREDENTIALS_FILE'], json_encode($token, JSON_THROW_ON_ERROR));
	}
}
