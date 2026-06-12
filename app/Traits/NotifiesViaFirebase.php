<?php

namespace App\Traits;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;


trait  NotifiesViaFirebase
{
    /**
     * Sends a Firebase notification to a list of device tokens or a topic.
     *
     * @param array $deviceTokens Array of device tokens to send notification to.
     * @param string|null $topic Topic to send notification to if specified.
     * @param string $title Title of the notification.
     * @param string $body Body text of the notification.
     * @param mixed $data Additional data to include in the notification.
     */
    public function sendFirebaseNotification(array $deviceTokens, ?string $topic, string $title, string $body, $data): void
    {
        $notificationData = [
            'title' => $title,
            'body' => $body, // Loại bỏ thẻ HTML để tránh lỗi
            'data' => $data
        ];

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($notificationData)
            ->withAndroidConfig(AndroidConfig::fromArray([
                'notification' => [
                    'sound' => 'default'
                ],
            ]))
            ->withApnsConfig(ApnsConfig::fromArray([
                'payload' => [
                    'aps' => [
                        'sound' => 'default'
                    ],
                ]
            ]));

        if ($topic) {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData($notificationData);
            $this->sendMessage($message);
        } else {
            foreach ($deviceTokens as $token) {
                $this->sendMessage($message->withChangedTarget('token', $token));
            }
        }
    }


    /**
     * Sends the message using Firebase messaging service.
     *
     * @param mixed $message The Firebase message object.
     */
    private function sendMessage(mixed $message): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('firebase_credentials.json'));
            $messaging = $factory->createMessaging();
            $messaging->send($message);
            Log::info('Firebase notification sent successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to send Firebase notification', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }


    /**
     * Registers a device token to a specific Firebase topic.
     *
     * @param string $deviceToken The device token to register.
     * @param string $topic The topic to subscribe to.
     * @return bool True if registration succeeded, false otherwise.
     */
    public function registerDeviceToTopic(string $deviceToken, string $topic): bool
    {
        $messaging = app('firebase.messaging');
        try {
            $messaging->subscribeToTopic($topic, [$deviceToken]);
            Log::info('Device registered to topic successfully.');
            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to register device to topic', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
