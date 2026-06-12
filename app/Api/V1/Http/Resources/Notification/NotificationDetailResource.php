<?php

namespace App\Api\V1\Http\Resources\Notification;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_message' => $this->short_message ?? $this->message,
            'message' => $this->message,
            'status' => $this->status->value,
            'created_at' => $this->created_at,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
        ];
    }
}
