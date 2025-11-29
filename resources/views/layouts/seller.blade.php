<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Center - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 h-screen flex overflow-hidden" x-data="{ sidebarOpen: false, logoutModal: false }">

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        x-transition></div>

    @include('partials.notification')

    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">

        <div class="h-16 flex items-center justify-center border-b border-gray-100">
            <h1 class="text-2xl font-bold" style="font-family: 'Fredoka', sans-serif;">
                <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
                <span
                    class="text-xs text-gray-400 ml-1 font-medium uppercase border border-gray-200 px-1 rounded">Seller</span>
            </h1>
        </div>

        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">

            <a href="{{ route('seller.dashboard') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg group transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('seller.dashboard') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>

            <div class="pt-4 pb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Manajemen Toko</div>

            <a href="{{ route('seller.products.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg group transition-colors {{ request()->routeIs('seller.products.index') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Produk Saya
            </a>

            <a href="{{ route('seller.orders.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg group transition-colors {{ request()->routeIs('seller.orders.index') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Pesanan Masuk
            </a>

            <a href="{{ route('seller.promotions.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg group transition-colors {{ request()->routeIs('seller.promotions.index') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                </svg>
                Promosi Saya
            </a>

            <div class="pt-4 pb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Keuangan</div>

            <a href="{{ route('seller.finance.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg group transition-colors {{ request()->routeIs('seller.finance.index') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Saldo Saya
            </a>

            <div class="pt-4 border-t border-gray-100 mt-4">
                <button @click="logoutModal = true"
                    class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-red-400 group-hover:text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar
                </button>
            </div>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="sticky top-0 bg-white/90 backdrop-blur-md border-b border-gray-200 z-30">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-gray-500 hover:text-gray-700 transition p-2 rounded-md hover:bg-gray-100 mr-2">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-1.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center px-3 py-1 bg-green-50 rounded-full border border-green-100">
                        <span class="relative flex h-2 w-2 mr-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-xs font-bold text-green-700 tracking-wide uppercase">Online</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center justify-center flex-1" x-data="{
                    date: '{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F') }}',
                    time: '{{ \Carbon\Carbon::now()->format('H:i:s') }}',
                    greeting: '{{ \Carbon\Carbon::now()->hour < 10 ? 'Selamat Pagi ☀️' : (\Carbon\Carbon::now()->hour < 15 ? 'Selamat Siang 🌤️' : (\Carbon\Carbon::now()->hour < 18 ? 'Selamat Sore 🌥️' : 'Selamat Malam 🌙')) }}',
                
                    initClock() {
                        const update = () => {
                            const now = new Date();
                            this.date = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long' }).format(now);
                            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace('.', ':');
                
                            const h = now.getHours();
                            if (h < 10) this.greeting = 'Selamat Pagi ☀️';
                            else if (h < 15) this.greeting = 'Selamat Siang 🌤️';
                            else if (h < 18) this.greeting = 'Selamat Sore 🌥️';
                            else this.greeting = 'Selamat Malam 🌙';
                        };
                        setInterval(update, 1000);
                    }
                }"
                    x-init="initClock()">

                    <div
                        class="flex items-center bg-gray-50 border border-gray-200 rounded-full px-5 py-1.5 shadow-sm group hover:border-green-200 transition-colors">
                        <span class="text-sm font-semibold text-gray-700 mr-3" x-text="greeting">
                            {{ \Carbon\Carbon::now()->hour < 10 ? 'Selamat Pagi ☀️' : (\Carbon\Carbon::now()->hour < 15 ? 'Selamat Siang 🌤️' : (\Carbon\Carbon::now()->hour < 18 ? 'Selamat Sore 🌥️' : 'Selamat Malam 🌙')) }}
                        </span>

                        <div class="h-4 w-px bg-gray-300 mx-1"></div>

                        <span class="text-sm text-gray-500 mx-3" x-text="date">
                            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F') }}
                        </span>

                        <div class="h-4 w-px bg-gray-300 mx-1"></div>

                        <span class="text-sm font-mono font-bold text-green-600 ml-3 min-w-20" x-text="time">
                            {{ \Carbon\Carbon::now()->format('H:i:s') }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">

                    <a href="#"
                        class="relative p-2 text-gray-400 hover:bg-gray-100 hover:text-green-600 rounded-lg transition-all group"
                        title="Pesan Pelanggan">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @if (isset($unreadChatCount) && $unreadChatCount > 0)
                            <span
                                class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold border-2 border-white shadow-sm">
                                {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('seller.notifications.index') }}"
                        class="relative p-2 text-gray-400 hover:bg-gray-100 hover:text-green-600 rounded-lg transition-all group"
                        title="Notifikasi">

                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>

                        @if (isset($unreadNotifCount) && $unreadNotifCount > 0)
                            <span
                                class="absolute top-1 right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold border-2 border-white shadow-sm transform translate-x-1 -translate-y-1">
                                {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                            </span>
                        @endif
                    </a>

                    <div class="h-8 w-px bg-gray-200 mx-1"></div>

                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded-xl transition-all border border-transparent hover:border-gray-100 group">

                            <div
                                class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center shadow-sm ring-2 ring-gray-100 group-hover:ring-green-200 transition bg-white">
                                @if (Auth::user()->seller && Auth::user()->seller->logo)
                                    <img src="{{ asset(Auth::user()->seller->logo) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-linear-to-br from-gray-800 to-gray-900 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr(Auth::user()->seller->name ?? Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            <div class="hidden md:block text-left">
                                <p class="text-sm font-bold text-gray-700 leading-none truncate max-w-[120px]">
                                    {{ Auth::user()->seller->name ?? 'Toko Baru' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1 leading-none truncate max-w-[120px]">
                                    {{ Auth::user()->name }}
                                </p>
                            </div>

                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200"
                                :class="{ 'rotate-180': dropdownOpen }" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50"
                            style="display: none;">

                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 rounded-t-xl md:hidden">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ Auth::user()->seller->name ?? 'Toko Baru' }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->name }}</p>
                            </div>

                            <div class="px-2 space-y-1 pt-1">
                                <a href="{{ route('seller.profile.index') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profil Toko
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>

                                <button @click="logoutModal = true"
                                    class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors group">
                                    <svg class="w-5 h-5 mr-3 text-red-400 group-hover:text-red-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 bg-gray-50 relative overflow-y-auto h-screen" x-data="{ showContent: false }"
            x-init="setTimeout(() => showContent = true, 150)">
            <div x-show="showContent" style="display: none;"
                x-transition:enter="transition-all ease-out duration-700"
                x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                class="min-h-full">
                @yield('content')
            </div>

        </main>
    </div>

    <div x-show="logoutModal" class="fixed inset-0 z-999 flex items-center justify-center p-4"
        style="display: none;">
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
                <a href="{{ route('auth.logout') }}"
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition">
                    Ya, Keluar
                </a>
                <button type="button" @click="logoutModal = false"
                    class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

</body>

</html>
