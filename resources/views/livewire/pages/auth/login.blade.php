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

<div x-data="{ showPassword: false }">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="auth-text text-xl font-bold text-gray-800 tracking-tight">ยินดีต้อนรับกลับ</h1>
        <p class="auth-text text-sm text-gray-600 mt-1">เข้าสู่ระบบเพื่อจัดการงานของคุณ</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <label for="email" class="auth-text block text-sm font-medium text-gray-700 mb-1.5">อีเมล</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fa-solid fa-envelope text-erms-muted"></i>
                </div>
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-erms-border bg-white/80 py-2.5 pl-10 pr-4 text-sm text-erms-text placeholder-erms-muted/60 transition-all duration-200 focus:border-erms-blue focus:bg-white focus:ring-2 focus:ring-erms-blue/15 focus:outline-none" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="auth-text block text-sm font-medium text-gray-700 mb-1.5">รหัสผ่าน</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fa-solid fa-lock text-erms-muted"></i>
                </div>
                <input wire:model="form.password" id="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password" required autocomplete="current-password"
                    placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                    class="w-full rounded-lg border border-erms-border bg-white/80 py-2.5 pl-10 pr-11 text-sm text-erms-text placeholder-erms-muted/40 transition-all duration-200 focus:border-erms-blue focus:bg-white focus:ring-2 focus:ring-erms-blue/15 focus:outline-none" />
                <button type="button"
                    x-on:click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-erms-muted hover:text-erms-text transition-colors duration-150"
                    tabindex="-1"
                    aria-label="แสดง/ซ่อนรหัสผ่าน">
                    <i x-show="!showPassword" class="fa-solid fa-eye"></i>
                    <i x-show="showPassword" x-cloak class="fa-solid fa-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-erms-border bg-white/80 text-erms-blue focus:ring-2 focus:ring-erms-blue/20 transition-colors duration-150 cursor-pointer" />
                <span class="auth-text ms-2 text-sm text-gray-600 group-hover:text-gray-800 transition-colors duration-150">จดจำฉัน</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-text text-sm text-erms-blue/80 hover:text-erms-blue transition-colors duration-150 cursor-pointer" href="{{ route('password.request') }}" wire:navigate>
                    ลืมรหัสผ่าน?
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit"
            class="relative w-full flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-semibold text-white rounded-lg cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-erms-blue/40 focus:ring-offset-2 active:scale-[0.98]"
            style="background: linear-gradient(135deg, #4573d2 0%, #7c5cfc 100%);"
            onmouseover="this.style.boxShadow='0 4px 20px rgba(69,115,210,0.4)'"
            onmouseout="this.style.boxShadow='none'">
            <span wire:loading.remove wire:target="login" class="auth-text">เข้าสู่ระบบ</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span class="auth-text">กำลังเข้าสู่ระบบ...</span>
            </span>
        </button>
    </form>
</div>
