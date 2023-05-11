<?php

declare(strict_types = 1);

namespace App\Security;

use App\OAuthBundle\Facade\CreateUserFacade;
use App\TwitchApiBundle\Service\ApiService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private CreateUserFacade $createUserFacade,
        private ApiService $apiService,
        private RouterInterface $router
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->get('_route') === 'oauth';
    }

    public function authenticate(Request $request): Passport
    {
        if (!$request->query->get('code')) {
            throw new BadRequestHttpException();
        }

        $OAuthToken = $this->apiService->getOauthToken($request->query->get('code'));
        $validToken = $this->apiService->validateToken($OAuthToken->getAccessToken());
        $twitchUser = $this->apiService->getUser($OAuthToken->getAccessToken(), (int)$validToken->getUserId());
        //$user = $this->OAuthRegisterService->user($OAuthToken, $validToken, $twitchUser[0]);
        $user = $this->createUserFacade->registerUser($OAuthToken, $validToken, $twitchUser[0]);

        return new SelfValidatingPassport(new UserBadge($user->getUserName()), [new RememberMeBadge()]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $uri = $this->router->generate('default', ['_locale' => $request->getLocale()]);
        return new RedirectResponse($uri, Response::HTTP_FOUND);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $uri = $this->router->generate('default', ['_locale' => $request->getLocale()]);
        return new RedirectResponse($uri, Response::HTTP_FOUND);
        /*
         * If you would like this class to control what happens when an anonymous user accesses a
         * protected page (e.g. redirect to /login), uncomment this method and make this class
         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
         *
         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
         */
    }
}
