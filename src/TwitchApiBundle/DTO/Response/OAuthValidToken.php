<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Response;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class OAuthValidToken extends DataTransferObject
{


    public function __construct(
        public string $clientId,
        public string $login,
        /** @var string[] $scopes */
        public array $scopes,
        public string $userId,
        public int $expiresIn,
    )
    {
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

}
