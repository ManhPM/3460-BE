<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreatedNotification;
use App\Models\Order;

trait SendMail
{
    use HasRepositoryFromAdmin;
    public function sendOrderNotification(Order $order)
    {
        $settingRepository = $this->getSettingRepository();
        $orderRepository = $this->getOrderRepository();
        $order = $orderRepository->findOrFail($order->id);
        $adminEmail = $settingRepository->findByField('setting_key', 'email_notification')->plain_value;

        $emails = explode(',', $adminEmail);

        // Send email to the customer's email if valid
        if (isset($order['email']) && filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
            Mail::to($order['email'])->send(new OrderCreatedNotification($order));
        }

        // Send email to the admin emails
        foreach ($emails as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new OrderCreatedNotification($order));
            }
        }
    }
}
