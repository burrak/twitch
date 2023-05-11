<?php

declare(strict_types = 1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class HomepageController
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
        private Environment $twig,
    )
    {
    }

    #[Route('/{_locale}/{channelName}', name: 'default', requirements: ['_locale' => 'en|cs'])]
    public function asd(string $channelName, Request $request): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            $uri = $this->router->generate('subscription_detail', ['_locale' => $request->getLocale(), 'channelName' => $channelName]);
            return new RedirectResponse($uri, Response::HTTP_FOUND);
        }

        $response =  new Response($this->twig->render('pages/default.html.twig'));
        $response->headers->setCookie(new Cookie('streamer', $channelName, time() + (10 * 60)));
        return $response;
    }

    #[Route('/{channelName}')]
    public function defaultChannelUnlocalized(string $channelName, Request $request): Response
    {
        $uri = $this->router->generate('default', ['_locale' => $request->getLocale(), 'channelName' => $channelName]);

        return new RedirectResponse($uri);
    }

    #[Route('/')]
    public function defaultUnlocalized(Request $request): Response
    {
        return new Response($this->twig->render('pages/index.html.twig'));
    }
}
