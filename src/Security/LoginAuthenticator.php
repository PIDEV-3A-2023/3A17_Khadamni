<?php

namespace App\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    private $session;
    private $urlGenerator;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;

    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $code = $this->session->get('info');
        $userCodeInput = $request->request->get('code');
        if ($code != $userCodeInput) {
            return new Passport(
                new UserBadge('accessdenied'),
                new PasswordCredentials($request->request->get('password', '')),
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                ]
            );
        }
        
      
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        /*
        $etat = $user->getEtatUser();
        if ($etat == 0) {
            $this->session->getFlashBag()->add('error', 'Votre compte est suspendu.');
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        } else {*/
            $UserRole = $user->getIdRole()->getTypeRole();
            if ($UserRole == 'admin')
                return new RedirectResponse($this->urlGenerator->generate('app_admin_maindashboard'));
            else
                return new RedirectResponse($this->urlGenerator->generate('app_home'));



    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
