<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-erms-muted">
        ขอบคุณที่สมัครสมาชิก! ก่อนเริ่มใช้งาน กรุณายืนยันอีเมลของคุณโดยคลิกลิงก์ที่เราส่งไปให้ หากคุณไม่ได้รับอีเมล เรายินดีที่จะส่งให้ใหม่อีกครั้ง
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-erms-green">
            ลิงก์ยืนยันใหม่ได้ถูกส่งไปยังอีเมลที่คุณให้ไว้ตอนสมัครแล้ว
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            ส่งอีเมลยืนยันอีกครั้ง
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-sm text-erms-muted hover:text-erms-text rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-erms-blue/30 cursor-pointer">
            ออกจากระบบ
        </button>
    </div>
</div>
