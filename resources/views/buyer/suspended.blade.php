<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Dibatasi - Green Mart</title>
    
    {{-- Styles --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative">
        
        {{-- Hiasan Background Atas --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-red-500"></div>

        <div class="p-8 text-center">
            {{-- Ikon Gembok/Suspend --}}
            <div class="mx-auto w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Akun Dibekukan</h1>
            
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Halo <span class="font-bold text-gray-900">{{ Auth::user()->name }}</span>, akun Anda saat ini dinonaktifkan oleh Admin karena adanya pelanggaran kebijakan atau aktivitas yang mencurigakan.
            </p>

            {{-- Kotak Info --}}
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-8 text-left flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-xs font-bold text-red-800 uppercase mb-0.5">Apa artinya ini?</p>
                    <p class="text-xs text-red-600 leading-snug">
                        Anda tidak dapat melakukan pembelian, mengakses keranjang, atau fitur lainnya sampai akun dipulihkan.
                    </p>
                </div>
            </div>

            <div class="space-y-3">
                {{-- Tombol Bantuan --}}
                <a href="mailto:support@greenmart.com?subject=Banding Akun Suspended - {{ Auth::user()->email }}" 
                   class="flex items-center justify-center w-full py-3 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl transition shadow-lg transform hover:-translate-y-0.5 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Hubungi Admin
                </a>
                
                {{-- Tombol Logout (Route Logout Default) --}}
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-full py-3 bg-white border-2 border-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-50 hover:text-red-600 transition gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar (Logout)
                    </button>
                </form>
            </div>

        </div>
        
        {{-- Footer Kecil --}}
        <div class="bg-gray-50 py-3 text-center border-t border-gray-100">
            <p class="text-[10px] text-gray-400">&copy; {{ date('Y') }} Green Mart Security System</p>
        </div>

    </div>

</body>
</html>