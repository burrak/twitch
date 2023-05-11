<?php

declare(strict_types = 1);

namespace App\OAuthBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class OAuthController
{
    //private const MESSAGE_TYPE = 'Twitch-Eventsub-Message-Type';

    public function __construct(
        private RouterInterface $router
    ) {
    }

    #[Route(path: '/oauth', name: 'oauth', methods: [Request::METHOD_GET, Request::METHOD_POST], priority: 999)]
    public function oauthBroadcaster(Request $request): Response
    {
        $streamer = $request->cookies->get('streamer');
        $uri = $this->router->generate('subscription_detail', ['_locale' => $request->getLocale(), 'channelName' => $streamer]);
        return new RedirectResponse($uri);
    }

}
/*
{"subscription":{"id":"cc5142d4-d3f0-68d8-b3b7-938127658ea6","status":"enabled","type":"channel.subscribe","version":"1","condition":{"broadcaster_user_id":"44635596"},"transport":{"method":"webhook","callback":"null"},"created_at":"2022-10-16T22:00:49.0223517Z","cost":0},"event":{"user_id":"44635596","user_login":"testFromUser","user_name":"testFromUser","broadcaster_user_id":"44635596","broadcaster_user_login":"testBroadcaster","broadcaster_user_name":"testBroadcaster","tier":"1000","is_gift":false}}
*/
