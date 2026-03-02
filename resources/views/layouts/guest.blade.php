<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ERMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .auth-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                position: relative;
                overflow: hidden;
            }
            .auth-bg::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(ellipse 80% 50% at 20% 40%, rgba(79, 142, 247, 0.3), transparent),
                    radial-gradient(ellipse 60% 60% at 80% 20%, rgba(124, 92, 252, 0.25), transparent),
                    radial-gradient(ellipse 50% 70% at 60% 80%, rgba(69, 115, 210, 0.2), transparent);
                pointer-events: none;
            }
            .auth-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                pointer-events: none;
                will-change: transform;
            }
            @media (prefers-reduced-motion: no-preference) {
                .auth-orb { animation: orbFloat 20s ease-in-out infinite; }
                .auth-orb:nth-child(2) { animation-delay: -7s; animation-duration: 25s; }
                .auth-orb:nth-child(3) { animation-delay: -14s; animation-duration: 22s; }
            }
            @keyframes orbFloat {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -40px) scale(1.05); }
                66% { transform: translate(-20px, 20px) scale(0.95); }
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.88);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow:
                    0 8px 32px rgba(0, 0, 0, 0.1),
                    0 1px 2px rgba(0, 0, 0, 0.06),
                    inset 0 1px 0 rgba(255, 255, 255, 0.6);
            }
            .grid-pattern {
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
                background-size: 60px 60px;
                pointer-events: none;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-erms-text">
        <div class="auth-bg min-h-screen flex flex-col items-center justify-center px-4 py-8">
            <!-- Animated orbs -->
            <div class="auth-orb w-[500px] h-[500px] bg-blue-400/20 top-[-10%] left-[-5%]" style="position:absolute;"></div>
            <div class="auth-orb w-[400px] h-[400px] bg-purple-400/20 bottom-[-10%] right-[-5%]" style="position:absolute;"></div>
            <div class="auth-orb w-[300px] h-[300px] bg-indigo-300/15 top-[40%] left-[50%]" style="position:absolute;"></div>

            <!-- Grid pattern overlay -->
            <div class="grid-pattern"></div>

            <!-- Branding -->
            <div class="relative z-10 flex items-center gap-3 mb-8 animate-fade-in-up">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #fff 0%, #e8edfb 100%);">
                    <svg class="w-7 h-7 text-erms-blue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <span class="font-heading font-bold text-2xl text-white tracking-tight">ERMS</span>
                    <p class="text-xs text-white/60 -mt-0.5">ระบบจัดการงานองค์กร</p>
                </div>
            </div>

            <!-- Glass card -->
            <div class="relative z-10 w-full sm:max-w-[420px] glass-card rounded-2xl px-8 py-8 animate-fade-in-up" style="animation-delay: 80ms;">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="relative z-10 mt-8 text-xs text-white/40 animate-fade-in-up" style="animation-delay: 160ms;">
                &copy; {{ date('Y') }} ERMS &middot; Enterprise Resource Management System
            </p>
        </div>
    </body>
</html>
