<?php

namespace App\DTO;

class PushNotificationData
{
    public function __construct(
        public string $title,
        public string $message,
        public array $booking
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'booking' => $this->booking,
        ];
    }
}
