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
                    <svg class="w-[18px] h-[18px] text-erms-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
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
                    <svg class="w-[18px] h-[18px] text-erms-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
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
                    <svg x-show="!showPassword" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg x-show="showPassword" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
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
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="auth-text">กำลังเข้าสู่ระบบ...</span>
            </span>
        </button>
    </form>
</div>
