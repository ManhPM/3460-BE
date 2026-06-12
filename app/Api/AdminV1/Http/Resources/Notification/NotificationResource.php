<?php

namespace App\Api\AdminV1\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->fullname,
                    'email' => $this->user->email,
                ];
            }),
            'title' => $this->title,
            'short_message' => $this->short_message,
            'message' => $this->message,
            'status' => $this->status?->value ?? $this->status,
            'read_at' => $this->read_at,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
