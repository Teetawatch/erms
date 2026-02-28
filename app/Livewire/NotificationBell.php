<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $showDropdown = false;

    public function getUnreadCountProperty(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function getNotificationsProperty()
    {
        return auth()->user()->notifications()->take(10)->get();
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
