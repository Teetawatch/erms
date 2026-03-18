<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ERMS - ระบบจัดการทรัพยากรพนักงาน</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #4f8ef7, #7c5cfc);">
                            <i class="fa-solid fa-clipboard-check text-white"></i>
                        </div>
                        <span class="font-heading font-semibold text-lg text-erms-text">ERMS</span>
                    </div>
                    @if (Route::has('login'))
                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">แดชบอร์ด</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-erms-muted hover:text-erms-text transition">เข้าสู่ระบบ</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary text-sm">สมัครสมาชิก</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </nav>

            {{-- Hero --}}
            <div class="flex-1 flex flex-col items-center justify-center px-6 py-20">
                <div class="max-w-2xl text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-erms-blue/10 text-erms-blue text-sm font-medium mb-6">
                        <i class="fa-solid fa-bolt"></i>
                        ระบบจัดการทรัพยากรพนักงาน
                    </div>
                    <h1 class="font-heading font-bold text-4xl md:text-5xl text-erms-text mb-4 leading-tight">
                        จัดการโครงการ & ทีมงาน<br>
                        <span class="bg-gradient-to-r from-[#4f8ef7] to-[#7c5cfc] bg-clip-text text-transparent">อย่างมืออาชีพ</span>
                    </h1>
                    <p class="text-erms-muted text-lg mb-8 max-w-xl mx-auto">
                        ERMS ช่วยให้คุณติดตามโครงการ มอบหมายงาน และดูรายงานสรุปได้ง่ายดาย
                    </p>
                    @if (Route::has('login'))
                        <div class="flex items-center justify-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary px-6 py-3 text-base">ไปที่แดชบอร์ด</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary px-6 py-3 text-base">เริ่มใช้งาน</a>
                            @endauth
                        </div>
                    @endif
                </div>

                {{-- Feature Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-16 max-w-4xl w-full">
                    <div class="card p-6 text-center">
                        <div class="w-12 h-12 rounded-xl bg-erms-blue/10 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-folder text-2xl text-erms-blue"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-erms-text mb-2">จัดการโครงการ</h3>
                        <p class="text-sm text-erms-muted">สร้างโครงการ ติดตามความคืบหน้า มอบหมายงานให้ทีม</p>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="w-12 h-12 rounded-xl bg-erms-purple/10 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-chart-column text-2xl text-erms-purple"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-erms-text mb-2">รายงาน & วิเคราะห์</h3>
                        <p class="text-sm text-erms-muted">ดูรายงานสรุป ส่งออก PDF วิเคราะห์ประสิทธิภาพทีม</p>
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

