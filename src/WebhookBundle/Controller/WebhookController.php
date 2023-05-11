<?php

declare(strict_types = 1);

namespace App\WebhookBundle\Controller;

use App\WebhookBundle\Facade\EventFacade;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController
{
    private const MESSAGE_TYPE = 'Twitch-Eventsub-Message-Type';
    //private const HMAC_PREFIX = 'sha256=';

    public function __construct(
        private LoggerInterface $logger,
        private EventFacade $eventFacade
    ) {
    }

    #[Route(path: '/webhook', name: 'webhook', methods: [Request::METHOD_GET, Request::METHOD_POST], priority: 999)]
    public function index(Request $request): Response
    {
        if ($request->headers->has(self::MESSAGE_TYPE) && ($request->headers->get(self::MESSAGE_TYPE) === 'webhook_callback_verification')) {
            $data = json_decode($request->getContent(), true);
            $this->logger->log(Logger::DEBUG, $request->getContent());
            return new Response($data['challenge']);
        }

        try
        {
            $this->eventFacade->createEvent($request);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        return new Response(null, Response::HTTP_ACCEPTED);
    }

}
/*
{"subscription":{"id":"cc5142d4-d3f0-68d8-b3b7-938127658ea6","status":"enabled","type":"channel.subscribe","version":"1","condition":{"broadcaster_user_id":"44635596"},"transport":{"method":"webhook","callback":"null"},"created_at":"2022-10-16T22:00:49.0223517Z","cost":0},"event":{"user_id":"44635596","user_login":"testFromUser","user_name":"testFromUser","broadcaster_user_id":"44635596","broadcaster_user_login":"testBroadcaster","broadcaster_user_name":"testBroadcaster","tier":"1000","is_gift":false}}
*/
