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

<body class="bg-gray-50 h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">

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
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors group">
                        <svg class="w-5 h-5 mr-3 text-red-400 group-hover:text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-30">
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 mr-4"><svg class="w-6 h-6"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg></button>
                <h2 class="text-lg font-bold text-gray-800">@yield('title')</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->seller->name ?? 'Toko Baru' }}</p>
                </div>
                <div class="h-9 w-9 rounded-full bg-gray-200 overflow-hidden border border-gray-300">
                    @if (Auth::user()->seller && Auth::user()->seller->logo)
                        <img src="{{ asset(Auth::user()->seller->logo) }}" class="w-full h-full object-cover">
                    @else
                        <span
                            class="w-full h-full flex items-center justify-center font-bold text-gray-500">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    @endif
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
</body>

</html>
