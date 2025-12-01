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

    {{-- Font: Plus Jakarta Sans (Utama) & Fredoka (Logo) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">

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

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

{{-- PERBAIKAN 1: x-data dipindah ke body agar state logoutModal bisa diakses dari mana saja (termasuk modal di bawah) --}}

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen selection:bg-green-100 selection:text-green-700"
    x-data="{ mobileMenu: false, userMenu: false, logoutModal: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/80 transition-all duration-300"
        :class="{ 'shadow-sm': scrolled }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 gap-8">

                {{-- 1. LOGO --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('buyer.home') }}" class="flex items-center gap-2 group">
                        <div
                            class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center text-white shadow-green-200 shadow-lg group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-bold leading-none" style="font-family: 'Fredoka', sans-serif;">
                                <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
                            </h1>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-0.5">Segar &
                                Alami</span>
                        </div>
                    </a>
                    <div class="hidden md:flex items-center space-x-1 ml-3">
                        <a href="{{ route('buyer.home') }}"
                            class="px-3 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('buyer.home') ? 'text-green-600 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-gray-50' }}">
                            Beranda
                        </a>
                        <a href="{{ route('buyer.sellers.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('buyer.sellers.*') ? 'text-green-600 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-gray-50' }}">
                            Mitra Toko
                        </a>
                    </div>
                </div>

                {{-- 2. SEARCH BAR (Desktop) - PERBAIKAN 2: Hanya muncul di Home --}}
                @if (request()->routeIs('buyer.home'))
                    <div class="hidden md:flex flex-1 max-w-2xl">
                        <form action="{{ route('buyer.products.index') }}" method="GET" class="w-full relative group">
                            <input type="text" name="search" placeholder="Mau cari apa hari ini?"
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 bg-gray-100 border-transparent focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 rounded-2xl text-sm transition-all outline-none font-medium placeholder-gray-400">
                            <div
                                class="absolute left-4 top-3 text-gray-400 group-focus-within:text-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Spacer jika tidak ada search bar agar layout tidak berantakan --}}
                    <div class="hidden md:block flex-1"></div>
                @endif

                {{-- 3. MENU KANAN --}}
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('buyer.cart.index') }}"
                        class="relative p-2.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-xl transition group"
                        title="Keranjang Belanja">
                        <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>

                        @auth
                            @php
                                $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity');
                            @endphp
                            @if ($cartCount > 0)
                                <span class="absolute top-1 right-1 flex h-4 w-4">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white items-center justify-center text-[9px] font-bold text-white">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                                </span>
                            @endif
                        @endauth
                    </a>

                    <a href="{{ route('buyer.notifications.index') }}"
                        class="relative p-3 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-xl transition group"
                        title="Notifikasi">
                        <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if (isset($unreadNotifCount) && $unreadNotifCount > 0)
                            <span class="absolute top-2 right-2 flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                            </span>
                        @endif
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
                                    <p class="text-[10px] font-medium text-green-600 mt-0.5">Member Setia</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden lg:block mr-2" :class="{ 'rotate-180': userMenu }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown Content --}}
                            <div x-show="userMenu" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                                style="display: none;">
                                <div class="px-4 py-3 border-b border-gray-100 lg:hidden">
                                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('buyer.orders.index') }}"
                                    class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Pesanan Saya
                                </a>
                                <a href="{{ route('buyer.profile.index') }}"
                                    class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Edit Profil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>

                                {{-- Button Trigger Modal Logout --}}
                                <button type="button" @click="userMenu = false; logoutModal = true"
                                    class="flex w-full items-center px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-b-xl">
                                    <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.login') }}"
                            class="text-sm font-bold text-gray-600 hover:text-green-600 px-3 py-2">Masuk</a>
                        <a href="{{ route('auth.register') }}"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-green-200 transform hover:-translate-y-0.5">Daftar</a>
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
        <div x-show="mobileMenu" x-transition x-cloak class="md:hidden bg-white border-b border-gray-200 shadow-lg">
            <div class="px-4 pt-4 pb-6 space-y-2">

                {{-- Search Mobile: Hanya di Home --}}
                @if (request()->routeIs('buyer.home'))
                    <form action="{{ route('buyer.products.index') }}" method="GET" class="relative mb-4">
                        <input type="text" name="search" placeholder="Cari produk..."
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 transition">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </form>
                @endif

                @auth
                    <div class="flex items-center gap-3 px-3 py-2 mb-4 border-b border-gray-100 pb-4">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=10b981&color=fff' }}"
                            class="w-10 h-10 rounded-full object-cover border border-gray-200">
                        <div>
                            <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                @endauth

                {{-- NAVIGATION LINKS --}}

                <a href="{{ route('buyer.home') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('buyer.home') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('buyer.home') ? 'text-green-600' : 'text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>

                <a href="{{ route('buyer.sellers.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('buyer.sellers.*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('buyer.sellers.*') ? 'text-green-600' : 'text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Mitra Toko
                </a>

                <a href="{{ route('buyer.cart.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('buyer.cart.*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('buyer.cart.*') ? 'text-green-600' : 'text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Keranjang
                </a>

                @auth
                    <a href="{{ route('buyer.orders.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('buyer.orders.*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('buyer.orders.*') ? 'text-green-600' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Pesanan Saya
                    </a>
                    <a href="{{ route('buyer.profile.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('buyer.profile.*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('buyer.profile.*') ? 'text-green-600' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil
                    </a>

                    <button type="button" @click="mobileMenu = false; logoutModal = true"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                @else
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <a href="{{ route('auth.login') }}"
                            class="block text-center px-4 py-3 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">Masuk</a>
                        <a href="{{ route('auth.register') }}"
                            class="block text-center px-4 py-3 rounded-xl text-sm font-bold text-white bg-green-600 hover:bg-green-700 transition shadow-lg shadow-green-200">Daftar</a>
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
                            class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center text-white font-black text-lg">
                            G</div>
                        <span class="font-bold text-xl text-gray-900">Green Mart</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">Pasar digital produk segar langsung dari petani
                        lokal ke meja makan Anda.</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-green-600">Cara Belanja</a></li>
                        <li><a href="#" class="hover:text-green-600">Pengiriman</a></li>
                        <li><a href="#" class="hover:text-green-600">Metode Pembayaran</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Tentang</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-green-600">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-green-600">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-green-600">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Ikuti Kami</h4>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-green-100 hover:text-green-600 transition">
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
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-medium text-green-600">System Operational</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- MODAL LOGOUT (MOVED HERE) --}}
    {{-- PERBAIKAN 2: Modal Logout Dipindahkan ke Bawah (Luar Nav) --}}
    <div x-show="logoutModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        style="display: none;" x-cloak>
        <div x-show="logoutModal" @click="logoutModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div x-show="logoutModal" class="relative w-full max-w-sm bg-white rounded-xl shadow-2xl overflow-hidden"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Konfirmasi Keluar</h3>
                <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin mengakhiri sesi ini?</p>
            </div>

            <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-2">
                {{-- FIX: Gunakan Form POST --}}
                <form action="{{ route('auth.logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition">
                        Ya, Keluar
                    </button>
                </form>

                <button type="button" @click="logoutModal = false"
                    class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

</body>

</html>
