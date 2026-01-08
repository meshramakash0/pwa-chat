<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Store a new push subscription
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $endpoint = $request->input('endpoint');
        $endpointHash = hash('sha256', $endpoint);

        // Update or create subscription
        PushSubscription::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'endpoint_hash' => $endpointHash,
            ],
            [
                'endpoint' => $endpoint,
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Remove a push subscription
     */
    public function destroy(Request $request)
    {
        $endpoint = $request->input('endpoint');
        $endpointHash = hash('sha256', $endpoint);

        PushSubscription::where('user_id', auth()->id())
            ->where('endpoint_hash', $endpointHash)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get VAPID public key
     */
    public function vapidPublicKey()
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key')
        ]);
    }
}

