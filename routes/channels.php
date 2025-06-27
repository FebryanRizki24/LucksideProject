<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('user.{userId}', function ($user, $userId) {
    \Log::info('Auth check on channel', [
        'user' => $user,
        'userId' => $userId,
        'matched' => (int) $user->id === (int) $userId
    ]);
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('admin-channel', function ($user) {
    return $user->hasRole('admin');
});