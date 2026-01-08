<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;

class PushNotificationService
{
    protected $webPush = null;
    protected $initialized = false;
    protected $initError = null;

    public function __construct()
    {
        // Delay initialization to avoid errors on page load
    }

    /**
     * Initialize WebPush (lazy loading)
     */
    protected function initialize(): bool
    {
        if ($this->initialized) {
            return $this->webPush !== null;
        }

        $this->initialized = true;

        try {
            // Check if required extensions are available
            if (!extension_loaded('gmp') && !extension_loaded('bcmath')) {
                $this->initError = 'GMP or BCMath extension required for push notifications';
                \Log::warning($this->initError);
                return false;
            }

            $auth = [
                'VAPID' => [
                    'subject' => config('webpush.vapid.subject'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ];

            $this->webPush = new \Minishlink\WebPush\WebPush($auth);
            return true;
        } catch (\Exception $e) {
            $this->initError = $e->getMessage();
            \Log::warning('WebPush initialization failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification to a user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        if (!$this->initialize()) {
            return; // Silently fail if push not available
        }

        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        foreach ($subscriptions as $pushSubscription) {
            $subscription = \Minishlink\WebPush\Subscription::create([
                'endpoint' => $pushSubscription->endpoint,
                'publicKey' => $pushSubscription->p256dh_key,
                'authToken' => $pushSubscription->auth_token,
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => '/fav.png',
                'badge' => '/fav.png',
                'tag' => 'chat-message-' . ($data['sender_id'] ?? 'unknown'),
                'data' => $data,
            ]);

            $this->webPush->queueNotification($subscription, $payload);
        }

        // Send all queued notifications
        foreach ($this->webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            
            if ($report->isSuccess()) {
                continue;
            }
            
            // If subscription is expired or invalid, remove it
            if ($report->isSubscriptionExpired()) {
                $endpointHash = hash('sha256', $endpoint);
                PushSubscription::where('endpoint_hash', $endpointHash)->delete();
            }
        }
    }
}
