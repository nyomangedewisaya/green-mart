<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Akun - Green Mart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-grid-slate-100 {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px), linear-gradient(to bottom, #f1f5f9 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col relative">

    <div class="absolute inset-0 bg-grid-slate-100 [mask:linear-gradient(0deg,white,rgba(255,255,255,0.6))] -z-10 fixed"></div>

    <nav class="w-full border-b border-gray-100 bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-green-600 tracking-tight" style="font-family: 'Fredoka'">Green</span>
                <span class="text-2xl font-bold text-amber-500 tracking-tight" style="font-family: 'Fredoka'">Mart</span>
                <span class="hidden sm:inline-block px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase rounded ml-2">Seller Area</span>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <div class="h-8 w-px bg-gray-200 mx-1 hidden sm:block"></div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 transition flex items-center gap-2">
                        Keluar
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center p-6 py-12">
        <div class="max-w-2xl w-full">
            @if($statusType == 'suspended')
                <div class="bg-white rounded-2xl shadow-xl border border-red-100 overflow-hidden">
                    <div class="bg-red-50 px-8 py-10 text-center border-b border-red-100">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm ring-8 ring-red-50">
                            <svg class="w-10 h-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Akses Toko Dibekukan</h1>
                        <p class="text-gray-600 mt-2 max-w-md mx-auto text-sm">
                            Kami mendeteksi aktivitas yang tidak sesuai dengan kebijakan komunitas Green Mart pada akun Anda.
                        </p>
                    </div>
                    
                    <div class="p-8">
                        <div class="flex gap-4 items-start p-4 bg-gray-50 rounded-xl border border-gray-200 mb-6">
                            <svg class="w-6 h-6 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Apa yang harus saya lakukan?</h4>
                                <p class="text-sm text-gray-600 mt-1">Jika Anda merasa ini adalah kesalahan, silakan ajukan banding melalui tim dukungan kami. Harap siapkan bukti pendukung.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                Hubungi Dukungan
                            </a>
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                                Coba Akses Dashboard
                            </a>
                        </div>
                    </div>
                </div>

            @else
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    
                    <div class="px-8 py-10 text-center bg-linear-to-b from-white to-gray-50/50">
                        <div class="inline-flex p-3 bg-yellow-50 rounded-full mb-4 shadow-sm">
                            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Pendaftaran Diterima!</h1>
                        <p class="text-gray-500 mt-2 text-sm max-w-lg mx-auto">
                            Terima kasih telah bergabung. Data toko Anda saat ini sedang dalam antrean verifikasi oleh tim admin kami.
                        </p>
                    </div>

                    <div class="bg-white px-8 py-8 border-t border-gray-100">
                        <div class="relative pl-4">
                            <div class="absolute left-[19px] top-2 bottom-4 w-0.5 bg-gray-200"></div>

                            <div class="relative flex items-start mb-8 group">
                                <div class="absolute left-0 bg-white py-1"> <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 shadow-sm border-2 border-white ring-1 ring-green-100">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                </div>
                                <div class="ml-16 pt-1">
                                    <h3 class="text-sm font-bold text-gray-900">Pendaftaran Akun</h3>
                                    <p class="text-xs text-green-600 font-medium mt-0.5">Selesai pada {{ Auth::user()->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            <div class="relative flex items-start mb-8">
                                <div class="absolute left-0 bg-white py-1">
                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200 border-2 border-white ring-1 ring-blue-100 relative z-10">
                                        <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </div>
                                </div>
                                <div class="ml-16 pt-1">
                                    <h3 class="text-sm font-bold text-blue-700">Verifikasi Data Toko</h3>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">Admin sedang memeriksa kelengkapan data Anda. Proses ini biasanya memakan waktu 1x24 jam kerja.</p>
                                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5 animate-ping"></span>
                                        Sedang Berlangsung
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex items-start">
                                <div class="absolute left-0 bg-white py-1">
                                    <div class="w-10 h-10 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center text-gray-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    </div>
                                </div>
                                <div class="ml-16 pt-2">
                                    <h3 class="text-sm font-bold text-gray-400">Toko Aktif</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Siap untuk berjualan</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="p-8 bg-gray-50 border-t border-gray-100 text-center">
                        <a href="{{ route('seller.dashboard') }}" class="w-full block bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm">
                            Segarkan Status
                        </a>
                        <p class="text-xs text-gray-400 mt-4">Butuh bantuan mendesak? <a href="https://wa.me/6281234567890" class="text-green-600 hover:underline font-medium">Chat WhatsApp Admin</a></p>
                    </div>
                </div>
            @endif

        </div>
    </main>

</body>
</html>