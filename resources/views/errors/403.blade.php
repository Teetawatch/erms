<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>403 - ไม่มีสิทธิ์เข้าถึง | ERMS</title>

        <!-- Fonts -->

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-erms-bg text-erms-text">
        <div class="min-h-screen flex flex-col">
            {{-- Navbar --}}
            <nav class="bg-white/80 backdrop-blur-md border-b border-erms-border sticky top-0 z-50">
                <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm transition-transform duration-200 group-hover:scale-105" style="background: linear-gradient(135deg, #4f8ef7, #7c5cfc);">
                            <i class="fa-solid fa-clipboard-check text-white"></i>
                        </div>
                        <span class="font-heading font-semibold text-lg text-erms-text">ERMS</span>
                    </a>
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">แดชบอร์ด</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-erms-muted hover:text-erms-text transition">เข้าสู่ระบบ</a>
                        @endauth
                    </div>
                </div>
            </nav>

            {{-- Content --}}
            <div class="flex-1 flex items-center justify-center px-6 py-16">
                <div class="max-w-md w-full text-center">
                    {{-- Shield Icon --}}
                    <div class="relative mx-auto mb-8 w-28 h-28">
                        <div class="absolute inset-0 rounded-full bg-erms-red/5 animate-pulse"></div>
                        <div class="absolute inset-3 rounded-full bg-erms-red/10"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-erms-red/90 to-erms-pink/80 flex items-center justify-center shadow-lg shadow-erms-red/20">
                                <i class="fa-solid fa-shield-halved text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Error Code --}}
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-erms-red/10 text-erms-red text-sm font-semibold mb-4">
                        <i class="fa-solid fa-lock text-xs"></i>
                        403
                    </div>

                    {{-- Message --}}
                    <h1 class="font-heading font-bold text-2xl md:text-3xl text-erms-text mb-3">
                        ไม่มีสิทธิ์เข้าถึง
                    </h1>
                    <p class="text-erms-muted text-[15px] leading-relaxed mb-8 max-w-sm mx-auto">
                        {{ $exception->getMessage() ?: 'คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้ หากคิดว่าเกิดข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ' }}
                    </p>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/dashboard') }}" class="btn-secondary px-5 py-2.5 w-full sm:w-auto">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            ย้อนกลับ
                        </a>
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-5 py-2.5 w-full sm:w-auto">
                            <i class="fa-solid fa-house text-xs"></i>
                            ไปหน้าแดชบอร์ด
                        </a>
                    </div>

                    {{-- Help Link --}}
                    <div class="mt-8 pt-6 border-t border-erms-border-light">
                        <p class="text-erms-muted text-2xs">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            หากต้องการสิทธิ์เพิ่มเติม กรุณาติดต่อผู้ดูแลระบบ
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="py-6 text-center text-sm text-erms-muted border-t border-erms-border">
                ERMS &copy; {{ date('Y') }} — ระบบจัดการทรัพยากรพนักงาน
            </footer>
        </div>
    </body>
</html>
