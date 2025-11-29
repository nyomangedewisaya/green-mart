<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Green Mart - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

@php
    $r = request();
    $isDashboard = $r->routeIs('admin.dashboard');
    $isLaporan = $r->routeIs('admin.reports.*');
    $isNotifikasi = $r->routeIs('admin.notifications.*');
    $isCategory = $r->routeIs('admin.categories.*');
    $isProduct = $r->routeIs('admin.products.*');
    $isPromotion = $r->routeIs('admin.promotions.*');
    $isCourier = $r->routeIs('admin.couriers.*');
    $isVerification = $r->routeIs('admin.verification.*');
    $isSellerData = $r->routeIs('admin.sellers.*');
    $isBuyerData = $r->routeIs('admin.buyers.*');
    $isTransaction = $r->routeIs('admin.transactions.*');
    $isWithdrawal = $r->routeIs('admin.withdrawals.*');
    $isMasterData = $isCategory || $isProduct || $isPromotion || $isCourier;
    $isUserManagement = $isBuyerData || $isSellerData || $isVerification;
    $isFinanceGroup = $isTransaction || $isWithdrawal;
@endphp

<body class="flex h-screen bg-gray-50" x-data="{
    sidebarOpen: false,
    logoutModal: false,
    masterDataOpen: @json($isMasterData),
    userManagementOpen: @json($isUserManagement),
    financeOpen: @json($isFinanceGroup)
}">

    @include('partials.notification')

    <aside
        class="fixed inset-y-0 left-0 z-50 shrink-0 w-64 overflow-y-auto bg-white text-gray-700 border-r border-gray-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">

        <div class="flex items-center justify-center h-16.5 border-b border-gray-200">
            <h1 class="text-3xl font-bold" style="font-family: 'Fredoka', sans-serif;">
                <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
            </h1>
        </div>

        <nav class="p-4">
            <ul class="list-none space-y-1">

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-4 py-2.5 text-sm rounded-lg transition-colors group
                       {{ $isDashboard ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 mr-3 transition-colors {{ $isDashboard ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 011-1z" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                <li>
                    <button @click="masterDataOpen = !masterDataOpen"
                        class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-left rounded-lg transition-colors group
                            {{ $isMasterData ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ $isMasterData ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                                <path fill-rule="evenodd"
                                    d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Master Data
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': masterDataOpen }"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <ul x-show="masterDataOpen" x-transition {{ $isMasterData ? 'style=display:block' : 'x-cloak' }}
                        class="mt-1 space-y-1 list-none">
                        <li>
                            <a href="{{ route('admin.categories.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                               {{ $isCategory ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isCategory ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5a.997.997 0 01.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd" />
                                </svg>
                                Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                               {{ $isProduct ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isProduct ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 5v1H4.667a1.75 1.75 0 00-1.743 1.598l-.826 9.5A1.75 1.75 0 003.84 19H16.16a1.75 1.75 0 001.743-1.902l-.826-9.5A1.75 1.75 0 0015.333 6H14V5a4 4 0 00-8 0zm4-2.5A2.5 2.5 0 007.5 5v1h5V5A2.5 2.5 0 0010 2.5zM7.5 10a2.5 2.5 0 005 0V8.75a.75.75 0 011.5 0V10a4 4 0 01-8 0V8.75a.75.75 0 011.5 0V10z"
                                        clip-rule="evenodd" />
                                </svg>
                                Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.promotions.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                               {{ $isPromotion ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isPromotion ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 2a.75.75 0 01.75.75v.59l.715.596c1.696 1.413 2.563 3.59 2.534 5.806a3.617 3.617 0 00.294 1.832l.33 1.056c.393 1.253.023 2.62-.99 3.46l-.003.002a4.52 4.52 0 01-2.877 1.14c-1.083 0-2.098-.41-2.877-1.14l-.003-.002a2.998 2.998 0 01-.99-3.46l.33-1.056a3.617 3.617 0 00.294-1.832 7.768 7.768 0 002.534-5.806l.715-.597V2.75A.75.75 0 0110 2zM2.75 9.5a.75.75 0 01.75-.75h.05a3.25 3.25 0 013.245 3.096.75.75 0 01-1.5.071 1.75 1.75 0 00-1.745-1.667H3.5a.75.75 0 01-.75-.75zm10.15-1.784a6.266 6.266 0 00-2.044-4.684.75.75 0 011.06-1.06 7.767 7.767 0 012.535 5.806.75.75 0 01-1.5-.062h-.051z"
                                        clip-rule="evenodd" />
                                </svg>
                                Promosi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.couriers.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                               {{ $isCourier ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 transition-colors {{ $isCourier ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                </svg>
                                Kurir
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <button @click="userManagementOpen = !userManagementOpen"
                        class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-left rounded-lg transition-colors group
                        {{ $isUserManagement ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ $isUserManagement ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM1.615 16.428a1.224 1.224 0 01-.569-1.175 6.002 6.002 0 0111.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 017 18a9.953 9.953 0 01-5.385-1.572zM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 00-1.588-3.755 4.502 4.502 0 015.874 2.636.818.818 0 01-.36.98A7.465 7.465 0 0114.5 16z" />
                            </svg>
                            Manajemen User
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': userManagementOpen }" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <ul x-show="userManagementOpen" x-transition
                        {{ $isUserManagement ? 'style=display:block' : 'x-cloak' }} class="mt-1 space-y-1 list-none">

                        <li>
                            <a href="{{ route('admin.verification.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                           {{ $isVerification ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isVerification ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="flex-1">Verifikasi Seller</span>

                                @if (isset($pendingSellerCount) && $pendingSellerCount > 0)
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full min-w-5 h-5">
                                        {{ $pendingSellerCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.sellers.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                           {{ $isSellerData ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isSellerData ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.93 1.31a41.401 41.401 0 0110.14 0C16.194 1.45 17 2.414 17 3.517V6H3V3.517c0-1.103.806-2.068 1.93-2.207zM3 7.5h14v2h-14v-2zm0 3.5h14V17a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"
                                        clip-rule="evenodd" />
                                </svg>
                                Data Seller
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.buyers.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                           {{ $isBuyerData ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isBuyerData ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                Data Buyer
                            </a>
                        </li>

                    </ul>
                </li>

                <li>
                    <button @click="financeOpen = !financeOpen"
                        class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-left rounded-lg transition-colors group
                            {{ $isFinanceGroup ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ $isFinanceGroup ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M1 4a1 1 0 011-1h16a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V4zm12 4a3 3 0 11-6 0 3 3 0 016 0zM4 9a1 1 0 100-2 1 1 0 000 2zm13-1a1 1 0 11-2 0 1 1 0 012 0zM1.75 14.5a.75.75 0 000 1.5c4.417 0 8.693.603 12.749 1.73 1.111.309 2.251-.512 2.251-1.696v-.784a.75.75 0 00-1.5 0v.784a.272.272 0 01-.35.25A49.043 49.043 0 001.75 14.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Keuangan & Transaksi
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': financeOpen }"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <ul x-show="financeOpen" x-collapse {{ $isFinanceGroup ? 'style=display:block' : 'x-cloak' }}
                        class="mt-1 space-y-1 list-none">

                        <li>
                            <a href="{{ route('admin.transactions.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                            {{ $isTransaction ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isTransaction ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0 1 1 0 002 0zM7 6a1 1 0 011-1h3a1 1 0 011 1v1a1 1 0 01-1 1H8a1 1 0 01-1-1V6zm0 4a1 1 0 011-1h6a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H8a1 1 0 01-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Monitoring Transaksi
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.withdrawals.index') }}"
                                class="flex items-center pl-11 pr-4 py-2 text-sm rounded-lg group
                            {{ $isWithdrawal ? 'text-green-700 font-medium bg-green-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-3 transition-colors {{ $isWithdrawal ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                    <path fill-rule="evenodd"
                                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Pencairan Dana
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm rounded-lg transition-colors group
                   {{ $isLaporan ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 mr-3 transition-colors {{ $isLaporan ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                clip-rule="evenodd" />
                        </svg>
                        Laporan
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.notifications.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm rounded-lg transition-colors group
                   {{ $isNotifikasi ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 mr-3 transition-colors {{ $isNotifikasi ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                clip-rule="evenodd" />
                        </svg>
                        Notifikasi
                    </a>
                </li>

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <li>
                        <button @click="logoutModal = true"
                            class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-lg group transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-red-600"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            Keluar
                        </button>
                    </li>
                </div>

            </ul>
        </nav>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="sticky top-0 bg-white/90 backdrop-blur-md border-b border-gray-200 z-30">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-gray-500 hover:text-gray-700 transition p-2 rounded-md hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-1.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div
                        class="hidden lg:flex items-center ml-4 px-3 py-1 bg-green-50 rounded-full border border-green-100">
                        <span class="relative flex h-2 w-2 mr-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-xs font-medium text-green-700 tracking-wide">ONLINE</span>
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
                        class="flex items-center bg-gray-50 border border-gray-200 rounded-full px-5 py-1.5 shadow-sm">
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

                    <a href="{{ route('admin.chats.index') }}"
                        class="relative p-2 text-gray-400 hover:bg-gray-100 hover:text-green-600 rounded-lg transition-all group"
                        title="Pesan">

                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>

                        @if (isset($unreadChatCount) && $unreadChatCount > 0)
                            <span
                                class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold border-2 border-white">
                                {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.notifications.index') }}"
                        class="relative p-2 text-gray-400 hover:bg-gray-100 hover:text-green-600 rounded-lg transition-all"
                        title="Notifikasi">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span
                            class="absolute top-2.5 right-2.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </a>

                    <div class="h-8 w-px bg-gray-200 mx-1"></div>

                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded-xl transition-all border border-transparent hover:border-gray-100 group">
                            <div
                                class="w-9 h-9 rounded-full bg-linear-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white group-hover:ring-green-100 transition">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>

                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-gray-700 leading-none">
                                    {{ Auth::user()->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-400 mt-1 leading-none">Administrator</p>
                            </div>

                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600"
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
                                <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>

                            <div class="px-2 space-y-1 pt-1">
                                <a href="{{ route('admin.profile.index') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profil Saya
                                </a>
                                <a href="{{ route('admin.settings.index') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Pengaturan
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button @click="logoutModal = true"
                                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4 mr-3 text-red-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
