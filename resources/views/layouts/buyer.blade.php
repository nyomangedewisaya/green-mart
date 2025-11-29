<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Green Mart</title>

    {{-- Styles & Scripts (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font: Plus Jakarta Sans (Modern & Clean) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1fae5;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }

        /* Hide Scrollbar but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen selection:bg-emerald-100 selection:text-emerald-700">

    @include('partials.notification')

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/80 transition-all duration-300"
        x-data="{ mobileMenu: false, userMenu: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)"
        :class="{ 'shadow-sm': scrolled }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 gap-8">

                {{-- 1. LOGO --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('buyer.home') }}" class="flex items-center gap-2 group">
                        <div
                            class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-emerald-200 shadow-lg group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-extrabold text-xl tracking-tight text-gray-900 leading-none">GreenMart</span>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Segar &
                                Alami</span>
                        </div>
                    </a>
                </div>

                {{-- 2. SEARCH BAR (Desktop) --}}
                <div class="hidden md:flex flex-1 max-w-2xl">
                    <form action="{{ route('buyer.products.index') }}" method="GET" class="w-full relative group">
                        <input type="text" name="search" placeholder="Mau cari apa hari ini?"
                            value="{{ request('search') }}"
                            class="w-full pl-12 pr-4 py-3 bg-gray-100 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-2xl text-sm transition-all outline-none font-medium placeholder-gray-400">
                        <div
                            class="absolute left-4 top-3 text-gray-400 group-focus-within:text-emerald-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
                </div>

                {{-- 3. MENU KANAN --}}
                <div class="hidden md:flex items-center gap-3">

                    {{-- Keranjang --}}
                    <a href="#"
                        class="relative p-3 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition group"
                        title="Keranjang">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{-- @auth
                            @php $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count(); @endphp
                            @if ($cartCount > 0)
                                <span class="absolute top-1.5 right-1.5 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white items-center justify-center text-[8px] font-bold text-white">{{ $cartCount }}</span>
                                </span>
                            @endif
                        @endauth --}}
                    </a>

                    {{-- Notifikasi (Placeholder) --}}
                    <a href="#"
                        class="p-3 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition"
                        title="Notifikasi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </a>

                    <div class="h-8 w-px bg-gray-200 mx-1"></div>

                    @auth
                        {{-- User Dropdown --}}
                        <div class="relative">
                            <button @click="userMenu = !userMenu" @click.away="userMenu = false"
                                class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full hover:bg-gray-100 border border-transparent hover:border-gray-200 transition focus:outline-none">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=10b981&color=fff' }}"
                                    class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm">
                                <div class="text-left hidden lg:block pr-2">
                                    <p class="text-xs font-bold text-gray-900 leading-none">
                                        {{ Str::limit(Auth::user()->name, 12) }}</p>
                                    <p class="text-[10px] font-medium text-emerald-600 mt-0.5">Member Setia</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden lg:block mr-2" :class="{ 'rotate-180': userMenu }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown Content --}}
                            <div x-show="userMenu" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                                style="display: none;">
                                <div class="px-4 py-3 border-b border-gray-100 lg:hidden">
                                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="#"
                                    class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Pesanan Saya
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Edit Profil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-b-xl">
                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-bold text-gray-600 hover:text-emerald-600 px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">Daftar</a>
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenu = !mobileMenu"
                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-xl transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu (Dropdown) --}}
        <div x-show="mobileMenu" x-collapse class="md:hidden bg-white border-b border-gray-200 shadow-lg">
            <div class="px-4 pt-4 pb-6 space-y-3">
                <form action="{{ route('buyer.products.index') }}" method="GET" class="relative mb-4">
                    <input type="text" name="search" placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>

                @auth
                    <div class="flex items-center gap-3 px-3 py-2 mb-4 border-b border-gray-100 pb-4">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=10b981&color=fff' }}"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                @endauth

                <a href="{{ route('buyer.home') }}"
                    class="block px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">🏠
                    Beranda</a>
                <a href="#"
                    class="block px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">🛒
                    Keranjang</a>

                @auth
                    <a href="#"
                        class="block px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">📦
                        Pesanan Saya</a>
                    <a href="#"
                        class="block px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">👤
                        Profil</a>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50">🚪
                            Keluar</button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <a href="{{ route('auth.login') }}"
                            class="block text-center px-4 py-3 rounded-xl text-sm font-bold text-gray-700 bg-gray-100">Masuk</a>
                        <a href="#"
                            class="block text-center px-4 py-3 rounded-xl text-sm font-bold text-white bg-emerald-600">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-black text-lg">
                            G</div>
                        <span class="font-bold text-xl text-gray-900">Green Mart</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">Pasar digital produk segar langsung dari petani
                        lokal ke meja makan Anda.</p>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600">Cara Belanja</a></li>
                        <li><a href="#" class="hover:text-emerald-600">Pengiriman</a></li>
                        <li><a href="#" class="hover:text-emerald-600">Metode Pembayaran</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Tentang</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-emerald-600">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-emerald-600">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Ikuti Kami</h4>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-emerald-100 hover:text-emerald-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-pink-100 hover:text-pink-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-gray-400">&copy; 2025 Green Mart Indonesia. All rights reserved.</p>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-medium text-emerald-600">System Operational</span>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
