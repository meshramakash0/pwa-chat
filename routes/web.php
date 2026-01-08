<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return redirect()->route('login');
});

// PWA logout redirect (for session timeout)
Route::get('/logout-redirect', function () {
    if (auth()->check()) {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
})->name('logout.redirect');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat/{user}', [ChatController::class, 'index'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');
    Route::get('/chat/{user}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::get('/chat/{user}/read-status', [ChatController::class, 'getReadStatus'])->name('chat.read-status');
    Route::delete('/chat/message/{messageId}', [ChatController::class, 'destroy'])->name('chat.delete');
    Route::delete('/chat/{user}/delete-all', [ChatController::class, 'destroyAll'])->name('chat.delete-all');

    // Push notification routes
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    Route::get('/push/vapid-public-key', [PushSubscriptionController::class, 'vapidPublicKey'])->name('push.vapid');
});

Route::get('/chat', function () {
    $users = \App\Models\User::where('id', '!=', auth()->id())->get();
    return view('chat.users', compact('users'));
})->middleware('auth')->name('chat.users');


require __DIR__.'/auth.php';
