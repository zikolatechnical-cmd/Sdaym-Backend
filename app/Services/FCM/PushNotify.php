<?php

namespace App\Services\FCM;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushNotify
{
    protected $messaging;

    public function __construct()
    {
        $credentials = config('services.firebase.credentials');
        $credentials = str_starts_with($credentials, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $credentials)
            ? $credentials
            : base_path($credentials);

        $factory = (new Factory)->withServiceAccount($credentials);
        $this->messaging = $factory->createMessaging();
    }

    public function sendPushNotification($deviceToken, $title, $message, $notificationData = [])
    {
        if (! $deviceToken) {
            return ['success' => false, 'message' => 'Device token is missing'];
        }
        $notification = Notification::create($title, $message);
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($notificationData);

        $this->messaging->send($message);

        return ['success' => true, 'message' => 'Notification sent successfully'];
    }

    public function sendBulkNotification(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return null;
        }

        $notification = Notification::create($title, $body);

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->sendMulticast($message, $tokens);
    }

    public function subscribeToTopic(string $deviceToken, string $topic): array
    {
        return $this->messaging->subscribeToTopic($topic, $deviceToken);
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array|string
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body))
            ->withData(array_map(static fn ($value) => (string) $value, $data));

        return $this->messaging->send($message);
    }
}
