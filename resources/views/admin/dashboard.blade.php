@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

    <div x-data="{
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        }
    }" class="space-y-6 font-inter">

        <div
            class="relative overflow-hidden rounded-2xl bg-linear-to-br from-green-600 via-emerald-600 to-teal-600 p-8 shadow-lg text-white">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center space-x-2 mb-2 opacity-80">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span
                            class="text-sm font-medium uppercase tracking-wider">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
                    <p class="mt-2 text-green-50 text-lg max-w-xl">Laporan aktivitas hari ini siap. Performa
                        penjualan terlihat stabil.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center px-5 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl backdrop-blur-sm transition transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Kelola Produk
                    </a>
                    <a href="{{ route('admin.verification.index') }}"
                        class="flex items-center px-5 py-3 bg-white text-green-700 font-bold rounded-xl shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-1">
                        Verifikasi Seller
                        @if ($pendingSellers > 0)
                            <span class="ml-2 flex h-3 w-3 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="absolute -right-10 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute top-10 right-40 w-32 h-32 bg-yellow-400/20 rounded-full blur-2xl"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-green-50 text-green-600 rounded-xl"><svg class="w-6 h-6" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span
                        class="text-xs font-bold px-2 py-1 rounded-lg {{ $revenueGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%</span>
                </div>
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1" x-text="formatCurrency({{ $currentRevenue }})"></h3>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl"><svg class="w-6 h-6" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg></div>
                    <span
                        class="text-xs font-bold px-2 py-1 rounded-lg {{ $orderGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}%</span>
                </div>
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($currentOrders) }}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl"><svg class="w-6 h-6" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg></div>
                </div>
                <p class="text-sm text-gray-500">Rata-rata Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1" x-text="formatCurrency({{ $aov }})"></h3>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-orange-50 text-orange-600 rounded-xl"><svg class="w-6 h-6" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg></div>
                    <span class="text-xs font-bold text-orange-700 bg-orange-100 px-2 py-1 rounded-lg">Baru</span>
                </div>
                <p class="text-sm text-gray-500">Pengguna Baru</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($newUsersCount) }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <span class="w-1.5 h-6 bg-green-500 rounded-full mr-3"></span>
                        Tren Pendapatan
                    </h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">7 Hari
                        Terakhir</span>
                </div>
                <div class="relative flex-1 min-h-[300px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div
                class="lg:col-span-1 bg-linear-to-br from-gray-900 to-gray-800 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden group flex flex-col justify-between">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none group-hover:bg-white/10 transition duration-500">
                </div>

                <div class="relative z-10 mb-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold tracking-wide">Pusat Tindakan</h3>
                        <span class="text-3xl font-extrabold leading-none">{{ $totalActionNeeded }}</span>
                    </div>
                    <p class="text-sm text-gray-400">Tugas yang memerlukan persetujuan.</p>
                </div>

                <div class="relative z-10 grid grid-cols-2 gap-2.5 flex-1">

                    {{-- 1. Penarikan --}}
                    <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}"
                        class="flex flex-col justify-center items-center p-2.5 bg-white/5 hover:bg-white/10 border border-white/5 hover:border-yellow-400/30 rounded-xl transition group/item">
                        <span
                            class="text-xl font-bold {{ $pendingWithdrawals > 0 ? 'text-yellow-400' : 'text-gray-500' }}">{{ $pendingWithdrawals }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 group-hover/item:text-yellow-100">Withdraw</span>
                    </a>

                    {{-- 2. Verifikasi --}}
                    <a href="{{ route('admin.verification.index') }}"
                        class="flex flex-col justify-center items-center p-2.5 bg-white/5 hover:bg-white/10 border border-white/5 hover:border-blue-400/30 rounded-xl transition group/item">
                        <span
                            class="text-xl font-bold {{ $pendingSellers > 0 ? 'text-blue-400' : 'text-gray-500' }}">{{ $pendingSellers }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 group-hover/item:text-blue-100">Verifikasi</span>
                    </a>

                    {{-- 3. Promosi (BARU) --}}
                    <a href="{{ route('admin.promotions.index', ['status' => 'pending']) }}"
                        class="flex flex-col justify-center items-center p-2.5 bg-white/5 hover:bg-white/10 border border-white/5 hover:border-green-400/30 rounded-xl transition group/item">
                        <span
                            class="text-xl font-bold {{ ($pendingPromotions ?? 0) > 0 ? 'text-green-400' : 'text-gray-500' }}">{{ $pendingPromotions ?? 0 }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 group-hover/item:text-green-100">Iklan/Promo</span>
                    </a>

                    {{-- 4. Laporan --}}
                    <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}"
                        class="flex flex-col justify-center items-center p-2.5 bg-white/5 hover:bg-white/10 border border-white/5 hover:border-red-400/30 rounded-xl transition group/item">
                        <span
                            class="text-xl font-bold {{ $pendingReports > 0 ? 'text-red-400' : 'text-gray-500' }}">{{ $pendingReports }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 group-hover/item:text-red-100">Laporan</span>
                    </a>

                    {{-- 5. Order Unpaid (Opsional, bisa dibuat full width di bawah) --}}
                    <a href="{{ route('admin.transactions.index', ['status' => 'pending']) }}"
                        class="col-span-2 flex flex-row justify-between items-center px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/5 hover:border-orange-400/30 rounded-xl transition group/item">
                        <span class="text-xs text-gray-400 group-hover/item:text-orange-100">Transaksi Pending
                            (Unpaid)</span>
                        <span
                            class="text-lg font-bold {{ $pendingOrders > 0 ? 'text-orange-400' : 'text-gray-500' }}">{{ $pendingOrders }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col h-[400px]">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Top Seller</h3>
                    <a href="{{ route('admin.sellers.index') }}"
                        class="text-xs font-bold text-green-600 hover:underline">LIHAT SEMUA</a>
                </div>
                <div class="flex-1 overflow-y-auto p-2 custom-scroll">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topSellers as $index => $seller)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 flex items-center">
                                        <div
                                            class="w-8 h-8 shrink-0 flex items-center justify-center font-bold text-xs mr-3 rounded-lg {{ $index == 0 ? 'bg-yellow-100 text-yellow-700' : ($index == 1 ? 'bg-gray-200 text-gray-700' : ($index == 2 ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-500')) }}">
                                            {{ $index + 1 }}</div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $seller->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $seller->rating ?? '0.0' }} Rating</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="font-bold text-gray-900">{{ $seller->orders_count }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase">Sold</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-12 text-gray-400 italic">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col h-[400px]">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Produk Terlaris</h3>
                    <a href="{{ route('admin.products.index') }}"
                        class="text-xs font-bold text-green-600 hover:underline">LIHAT SEMUA</a>
                </div>
                <div class="flex-1 overflow-y-auto p-2 custom-scroll">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topProducts as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 flex items-center">
                                        <img src="{{ $item->product->image ? asset($item->product->image) : 'https://placehold.co/100x100/e0e0e0/757575?text=IMG' }}"
                                            class="w-10 h-10 rounded-lg object-cover border border-gray-100 shadow-sm mr-3 shrink-0">
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 truncate">{{ $item->product->name }}</p>
                                            <p class="text-xs text-green-600 font-mono">Rp
                                                {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg border border-green-100 shadow-sm group-hover:bg-green-100 transition-colors">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>

                                            <span class="text-sm font-bold leading-none pt-px">
                                                {{ $item->total_sold }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-12 text-gray-400 italic">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-[400px] flex flex-col">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Distribusi Pesanan</h3>
                <div class="flex-1 relative w-full flex justify-center items-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <span class="w-1.5 h-6 bg-purple-500 rounded-full mr-3"></span>
                    Transaksi Terbaru
                </h3>
                <a href="{{ route('admin.transactions.index') }}"
                    class="text-xs font-bold text-green-600 hover:underline">LIHAT SEMUA</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Invoice</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4"><span
                                        class="font-mono text-xs font-medium text-green-700 bg-green-100 px-2 py-1 rounded border">#{{ $order->order_code }}</span>
                                    <div class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->seller->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900 text-right"
                                    x-text="formatCurrency({{ $order->total_amount }})"></td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $order->status == 'completed' ? 'bg-green-100 text-green-700 border-green-200' : ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">{{ $order->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-400">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#9ca3af';

            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: @json($revenueLabels),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json($revenueData),
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointRadius: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: {
                                display: false
                            },
                            grid: {
                                borderDash: [4, 4]
                            },
                            ticks: {
                                callback: (val) => 'Rp ' + (val / 1000) + 'k'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled'],
                    datasets: [{
                        data: @json($chartStatusData),
                        backgroundColor: ['#facc15', '#60a5fa', '#a78bfa', '#4ade80', '#f87171'],
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 10px;
        }

        .custom-scroll:hover::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
        }
    </style>

@endsection
