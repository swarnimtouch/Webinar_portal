<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('webinar.{slug}.presence', function (User $user, string $slug) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'hand' => false,
    ];
});
Broadcast::channel('webinar.{slug}.chat', fn($user, $slug) => true);
