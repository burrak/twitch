<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\Service;

use App\TwitchApiBundle\DTO\Request\EventSub\EventSubRequest;
use App\TwitchApiBundle\DTO\Response\EventSub\EventSubResponse;
use App\TwitchApiBundle\DTO\Response\OAuthToken;
use App\TwitchApiBundle\DTO\Response\OAuthValidToken;
use App\TwitchApiBundle\DTO\Response\TwitchUser\TwitchUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private const URL_OAUTH_TOKEN = "/oauth2/token";
    private const URL_OAUTH_VALIDATE = "/oauth2/validate";
    private const URL_HELIX_USERS = "/helix/users";
    private const URL_EVENTSUB_SUBSCRIPTIONS = "/helix/eventsub/subscriptions";

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $redirectUri,
        private string $callbackUrl,
        private string $twitchApiUrl,
        private string $twitchIdUrl,
        private HttpClientInterface $client,
        private SerializerInterface $serializer
    )
    {
    }

    public function getOauthToken(string $code): OAuthToken
    {
        $oauth = $this->client->request(
            Request::METHOD_POST,
            $this->buildUrl(
                $this->twitchIdUrl,
                self::URL_OAUTH_TOKEN,
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $this->redirectUri,
                ]
            )
        );

        return $this->serializer->deserialize($oauth->getContent(), OAuthToken::class, 'json');
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function refreshOauthToken(string $token): OAuthToken
    {
        $oauth = $this->client->request(
            Request::METHOD_POST,
            $this->buildUrl(
                $this->twitchIdUrl,
                self::URL_OAUTH_TOKEN,
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $token,
                ]
            )
        );

        return $this->serializer->deserialize($oauth->getContent(), OAuthToken::class, 'json');
    }

    public function validateToken(string $token): OAuthValidToken
    {
        $validToken = $this->client->request(Request::METHOD_GET, $this->buildUrl($this->twitchIdUrl, self::URL_OAUTH_VALIDATE), ['headers' => ['Authorization' => 'OAuth ' . $token]]);

        return $this->serializer->deserialize($validToken->getContent(), OAuthValidToken::class, 'json');
    }

    /**
     * @return TwitchUser[]
     */
    public function getUser(string $token, int $userId): array
    {
        $user = $this->client->request(Request::METHOD_GET, $this->buildUrl($this->twitchApiUrl, self::URL_HELIX_USERS, ['id' => $userId]), ['headers' => ['Authorization' => 'Bearer ' . $token, 'Client-Id' => $this->clientId]]);

        return $this->serializer->deserialize(json_encode($user->toArray()['data']), TwitchUser::class . '[]', 'json');
    }

    /**
     * @param string $type
     * @param int $broadcasterUserId
     * @return EventSubResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function createSubscription(string $type, int $broadcasterUserId): EventSubResponse
    {
        $request = new EventSubRequest($type, "1", (string) $broadcasterUserId, "webhook", $this->callbackUrl, "secret123456");

        $event = $this->client->request(
            Request::METHOD_POST,
            $this->buildUrl(
                $this->twitchApiUrl,
                self::URL_EVENTSUB_SUBSCRIPTIONS
            ),
            [
                'body' => $this->serializer->serialize(
                    $request,
                    'json'
                ),
                'headers' =>
                    [
                        'Authorization' => 'Bearer ' . $this->getAccessToken(),
                        'Client-Id' => $this->clientId,
                        'Content-Type' => 'application/json',
                    ],
            ]
        );

        return $this->serializer->deserialize(json_encode($event->toArray()['data'][0]), EventSubResponse::class, 'json');
    }

    /**
     * @param int $twitchId
     * @return EventSubResponse[]
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function fetchSubscriptionsByUser(int $twitchId): array
    {
        $response = $this->client->request(
            Request::METHOD_GET,
            $this->buildUrl(
                $this->twitchApiUrl,
                self::URL_EVENTSUB_SUBSCRIPTIONS,
                [
                    'user_id' => $twitchId,
                ]
            ),
            [
                'headers' =>
                    [
                        'Authorization' => 'Bearer ' . $this->getAccessToken(),
                        'Client-Id' => $this->clientId,
                    ],
            ]
        );

        return $this->serializer->deserialize(json_encode($response->toArray()['data']), EventSubResponse::class . '[]', 'json');
    }

    public function removeSubscription(string $id): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            $this->buildUrl(
                $this->twitchApiUrl,
                self::URL_EVENTSUB_SUBSCRIPTIONS,
                [
                    'id' => $id,
                ]
            ),
            [
                'headers' =>
                    [
                        'Authorization' => 'Bearer ' . $this->getAccessToken(),
                        'Client-Id' => $this->clientId,
                    ],
            ]
        );
    }


    private function buildUrl(string $url, string $route, ?array $params = null): string
    {
        if ($params === null) {
            return $url . $route;
        }

        return $url . $route . "?" . http_build_query($params);
    }

    private function getAccessToken(): string
    {
        $accessToken = $this->client->request(
            Request::METHOD_POST,
            $this->buildUrl(
                $this->twitchIdUrl,
                self::URL_OAUTH_TOKEN,
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                ]
            ),
            [
                'headers' =>
                    [
                        'Content-Type' => 'x-www-form-urlencoded',
                    ],
            ]
        );

        $acessArray = json_decode($accessToken->getContent(), true);

        return $acessArray['access_token'];
    }
}
