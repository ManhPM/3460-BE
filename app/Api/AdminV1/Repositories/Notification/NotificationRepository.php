<?php

namespace App\Api\AdminV1\Repositories\Notification;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Notification;

class NotificationRepository extends EloquentRepository implements NotificationRepositoryInterface
{
    public function getModel(): string
    {
        return Notification::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('user');

        // Column-specific filters (theo key trong columns config)
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('title') && !empty(request('title'))) {
            $query->where('title', 'like', "%" . request('title') . "%");
        }

        if (request()->has('short_message') && !empty(request('short_message'))) {
            $query->where('short_message', 'like', "%" . request('short_message') . "%");
        }

        if (request()->has('user_id') && !empty(request('user_id'))) {
            // Search by user fullname or email (user_id column search is for searching user fullname/email)
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('fullname', 'like', "%" . request('user_id') . "%")
                    ->orWhere('email', 'like', "%" . request('user_id') . "%");
            });
        }

        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
