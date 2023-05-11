<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Response\TwitchUser;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class TwitchUser extends DataTransferObject
{


    public function __construct(
        private string $id,
        private string $login,
        private string $displayName,
        private string $type,
        private string $broadcasterType,
        private string $description,
        private string $profileImageUrl,
        private string $offlineImageUrl,
        private int $viewCount,
        private string $email,
        private string $createdAt,
    )
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getBroadcasterType(): string
    {
        return $this->broadcasterType;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getProfileImageUrl(): string
    {
        return $this->profileImageUrl;
    }

    /**
     * @return string
     */
    public function getOfflineImageUrl(): string
    {
        return $this->offlineImageUrl;
    }

    /**
     * @return int
     */
    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

}
