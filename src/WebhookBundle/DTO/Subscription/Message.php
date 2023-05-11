<?php

declare(strict_types = 1);

namespace App\WebhookBundle\DTO\Subscription;

class Message
{
    /**
     * @param string $text
     * @param Emote[] $emotes
     */
    public function __construct(private string $text, private array $emotes)
    {
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array
     */
    public function getEmotes(): array
    {
        return $this->emotes;
    }

}
