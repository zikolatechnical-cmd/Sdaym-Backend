<?php

namespace App\Notifications;

use App\DTO\PushNotificationData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class NewNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    private $notifiable;

    public function __construct(private PushNotificationData $data) {}

    public function via(object $notifiable): array
    {
        $this->notifiable = $notifiable;

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->data->toArray();
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->data->toArray();
    }
}
