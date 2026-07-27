<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Dynamic Channel for Global Broadcasts, Course Topics, and Study Groups
 * Matches JavaScript client: window.Echo.channel('chat.' + currentChannelType + '.' + currentChannelId)
 */
Broadcast::channel('chat.{type}.{id}', function ($user, $type, $id) {
    // 1. Global main chat broadcast stream is open to all authenticated users
    if ($type === 'broadcast') {
        return true;
    }

    // 2. Course topic chat streams are open to all authenticated users
    if ($type === 'topic') {
        return true; 
    }

    // 3. Study Group chat stream: Verifies membership using your actual group_memberships table
    if ($type === 'group') {
        return $user->groups()
            ->where('group_discussions.id', $id)
            ->exists();
    }

    return false;
});