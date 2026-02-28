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
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        ระบบจัดการทรัพยากรพนักงาน
                    </div>
                    <h1 class="font-heading font-bold text-4xl md:text-5xl text-erms-text mb-4 leading-tight">
                        จัดการโครงการ & ทีมงาน<br>
                        <span class="bg-gradient-to-r from-[#4f8ef7] to-[#7c5cfc] bg-clip-text text-transparent">อย่างมืออาชีพ</span>
                    </h1>
                    <p class="text-erms-muted text-lg mb-8 max-w-xl mx-auto">
                        ERMS ช่วยให้คุณติดตามโครงการ มอบหมายงาน บันทึกเวลาทำงาน และดูรายงานสรุปได้ง่ายดาย
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 max-w-4xl w-full">
                    <div class="card p-6 text-center">
                        <div class="w-12 h-12 rounded-xl bg-erms-blue/10 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        </div>
                        <h3 class="font-heading font-semibold text-erms-text mb-2">จัดการโครงการ</h3>
                        <p class="text-sm text-erms-muted">สร้างโครงการ ติดตามความคืบหน้า มอบหมายงานให้ทีม</p>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="w-12 h-12 rounded-xl bg-erms-green/10 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-erms-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-heading font-semibold text-erms-text mb-2">บันทึกเวลา</h3>
                        <p class="text-sm text-erms-muted">จับเวลาทำงาน บันทึกชั่วโมง ดูสรุปรายสัปดาห์</p>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="w-12 h-12 rounded-xl bg-erms-purple/10 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-erms-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
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

