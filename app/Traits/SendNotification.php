<?php

namespace App\Traits;

use App\Models\Setting;

trait SendNotification
{
    use HasRepositoryFromAdmin, NotifiesViaFirebase;
    public function sendNotification($personId, $title, $message, $deviceToken = null, $type = 'user_id')
    {
        $notificationRepository = $this->getNotificationRepository();
        $notification = $notificationRepository->create([
            $type => $personId,
            'title' => $title,
            'short_message' => $message,
            'message' => $message,
        ]);
        if (env('IS_PRO')) {
            if ($notification && $deviceToken) {
                $this->sendFirebaseNotification([$deviceToken], null, $notification->title, $notification->message, $notification->id);
            }
        }
    }
}
