<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm text-erms-muted mb-1">อีเมล</label>
            <input wire:model="form.email" id="email" class="input-field" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm text-erms-muted mb-1">รหัสผ่าน</label>
            <input wire:model="form.password" id="password" class="input-field"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-erms-border bg-white text-erms-blue focus:ring-erms-blue/20" name="remember">
                <span class="ms-2 text-sm text-erms-muted">จดจำฉัน</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-erms-muted hover:text-erms-blue transition" href="{{ route('password.request') }}" wire:navigate>
                    ลืมรหัสผ่าน?
                </a>
            @endif

            <button type="submit" class="btn-primary ms-3">
                เข้าสู่ระบบ
            </button>
        </div>
    </form>
</div>
