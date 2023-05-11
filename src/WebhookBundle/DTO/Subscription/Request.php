<?php

declare(strict_types = 1);

namespace App\WebhookBundle\DTO\Subscription;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Request extends DataTransferObject
{
    public function __construct(private Subscription $subscription, private Event $event)
    {
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }
}
