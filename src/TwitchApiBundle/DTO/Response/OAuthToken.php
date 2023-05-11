<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Response;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class OAuthToken extends DataTransferObject
{
    public function __construct(
        private string $accessToken,
        private int $expiresIn,
        private string $refreshToken,
        /** @var string[] $scope */
        private array $scope,
        private string $tokenType,
    )
    {
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return array
     */
    public function getScope(): array
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }


}
