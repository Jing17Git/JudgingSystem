<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Admin-only private channel for live dashboard data and real-time score feeds
Broadcast::channel('admin.scores', function ($user) {
    return $user && $user->role === 'admin';
});

// Judge & Admin private channel for real-time submission progress and alerts
Broadcast::channel('judge.scores', function ($user) {
    return $user && in_array($user->role, ['judge', 'admin']);
});
