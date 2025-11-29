@extends('layouts.seller')
@section('title', 'Dashboard Toko')

@section('content')

    {{-- 1. DATA LAYER (TIDAK BERUBAH) --}}
    <script>
        window.dashboardData = {
            revenueLabels: @json($chartLabels ?? []), // Note: Sesuaikan nama variabel controller
            revenueData: @json($chartRevenue ?? []), // Note: Sesuaikan nama variabel controller
            // Gunakan data dari controller baru di bawah ini jika menggunakan alpine component
        };
    </script>

    <div class="space-y-8 font-inter">

        {{-- 2. HEADER BARU (SESUAI REQUEST) --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Halo,
                    {{ Auth::user()->seller->name ?? 'Seller' }} 👋</h1>
                <p class="text-gray-500 mt-1 text-sm font-medium">Semoga penjualan hari ini meningkat!</p>
            </div>

            <a href="{{ route('seller.products.index') }}"
                class="group flex items-center px-5 py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition shadow-lg transform hover:-translate-y-0.5">
                <div class="mr-2 p-1 bg-white/20 rounded-lg group-hover:bg-white/30 transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                Upload Produk
            </a>
        </div>

        {{-- 3. CARD NOTIFIKASI PESANAN (DESAIN BARU & LEBIH MENARIK) --}}
        @if (isset($ordersToShip) && $ordersToShip > 0)
            <div
                class="relative group overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 via-green-500 to-teal-500 p-1 shadow-lg shadow-green-200/50">
                {{-- Background Pattern Abstract --}}
                <div
                    class="absolute top-0 right-0 -mr-12 -mt-12 w-48 h-48 bg-white/20 blur-3xl rounded-full pointer-events-none group-hover:bg-white/30 transition duration-500">
                </div>
                <div
                    class="absolute bottom-0 left-0 -ml-12 -mb-12 w-32 h-32 bg-yellow-300/20 blur-2xl rounded-full pointer-events-none">
                </div>

                <div class="relative bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">

                        {{-- Icon & Text --}}
                        <div class="flex items-center gap-5 w-full sm:w-auto">
                            <div class="relative">
                                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-green-500"></span>
                                </span>
                                <div
                                    class="h-14 w-14 rounded-2xl bg-white/20 flex items-center justify-center shadow-inner border border-white/30 text-white">
                                    <svg class="w-8 h-8 drop-shadow-md" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight drop-shadow-sm">
                                    <span class="text-yellow-200 text-2xl mr-1">{{ $ordersToShip }}</span> Pesanan Siap
                                    Dikirim
                                </h3>
                                <p class="text-green-50 text-sm font-medium opacity-90 leading-tight mt-1">
                                    Pembeli menunggu! Segera proses agar dana cair lebih cepat.
                                </p>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}"
                            class="w-full sm:w-auto group/btn relative inline-flex items-center justify-center px-6 py-3 bg-white text-emerald-700 text-sm font-bold rounded-xl hover:bg-green-50 transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            <span>Proses Sekarang</span>
                            <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- CARD 1: REVENUE --}}
            <div
                class="relative bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-3xl shadow-xl text-white overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-green-500/20 rounded-full blur-3xl -mr-8 -mt-8 group-hover:bg-green-500/30 transition">
                </div>
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div class="p-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/5">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        {{-- Percentage Badge --}}
                        <div
                            class="flex items-center gap-1 px-2 py-1 rounded-lg border border-white/10 {{ $percentageChange >= 0 ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $percentageChange >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}" />
                            </svg>
                            <span class="text-[10px] font-bold">{{ number_format(abs($percentageChange), 1) }}% vs Bln
                                Lalu</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <p class="text-gray-400 text-sm font-medium">Total Pendapatan</p>
                        <h2 class="text-3xl lg:text-4xl font-black tracking-tight mt-1 text-white">
                            Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>

            {{-- CARD 2: ORDERS --}}
            <div
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition duration-300 group flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 01-2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Sukses</span>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Rasio Pesanan Selesai</p>
                    <div class="flex items-end gap-1.5 mt-1">
                        <h3 class="text-3xl font-black text-gray-900">{{ $completedOrders ?? 0 }}</h3>
                        <span class="text-sm text-gray-400 font-bold mb-1.5">/ {{ $totalOrders ?? 0 }} Total</span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5 mt-4 overflow-hidden">
                    <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-1000 ease-out relative"
                        style="width: {{ ($totalOrders ?? 0) > 0 ? (($completedOrders ?? 0) / $totalOrders) * 100 : 0 }}%">
                    </div>
                </div>
            </div>

            {{-- CARD 3: PRODUCTS --}}
            <div
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition duration-300 group flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2.5 bg-orange-50 text-orange-600 rounded-xl group-hover:bg-orange-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">Aktif</span>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Etalase Produk</p>
                    <div class="flex items-end gap-1.5 mt-1">
                        <h3 class="text-3xl font-black text-gray-900">{{ $activeProducts ?? 0 }}</h3>
                        <span class="text-sm text-gray-400 font-bold mb-1.5">/ {{ $totalProducts ?? 0 }} Total</span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5 mt-4 overflow-hidden">
                    <div class="bg-orange-500 h-2.5 rounded-full transition-all duration-1000 ease-out"
                        style="width: {{ ($totalProducts ?? 0) > 0 ? (($activeProducts ?? 0) / $totalProducts) * 100 : 0 }}%">
                    </div>
                </div>
            </div>

            {{-- CARD 4: RATING (IMPROVED) --}}
            <div
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <h3 class="text-2xl font-black text-gray-900 leading-none">{{ number_format($ratingAvg ?? 0, 1) }}
                        </h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $totalReviews ?? 0 }} Ulasan</p>
                    </div>
                </div>

                {{-- Rating Bars Breakdown --}}
                <div class="space-y-1.5">
                    @foreach ($ratingDist as $star => $data)
                        <div class="flex items-center gap-2 text-xs">
                            <span class="flex items-center w-3 gap-0.5 font-bold text-gray-500">{{ $star }}<svg
                                    class="w-2 h-2 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg></span>
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $data['percentage'] }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CHART SECTION (ALPINE JS + CHART JS) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto items-stretch">

            {{-- MAIN CHART --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col"
                x-data="chartComponent()" x-init="initChart()">

                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <span class="w-1.5 h-6 bg-green-500 rounded-full mr-3"></span>
                        Grafik Pendapatan
                    </h3>

                    {{-- Toggle Buttons --}}
                    <div class="flex bg-gray-100 p-1 rounded-xl">
                        <button @click="mode = 'weekly'; updateChart()"
                            :class="mode === 'weekly' ? 'bg-white text-gray-900 shadow-sm' :
                                'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all duration-200">
                            Mingguan
                        </button>
                        <button @click="mode = 'monthly'; updateChart()"
                            :class="mode === 'monthly' ? 'bg-white text-gray-900 shadow-sm' :
                                'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all duration-200">
                            Bulanan
                        </button>
                    </div>
                </div>

                <div class="relative flex-1 min-h-[300px] w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- TOP PRODUCTS --}}
            <div class="lg:col-span-1 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-6 pb-2 border-b border-gray-100">
                    Produk Terlaris</h3>
                <div class="space-y-4 flex-1 overflow-y-auto max-h-80 custom-scroll pr-2">
                    @forelse($topProducts as $index => $item)
                        <div
                            class="flex items-center gap-3 group cursor-pointer hover:bg-gray-50 p-2 rounded-xl transition">
                            <span
                                class="flex items-center justify-center w-6 h-6 text-xs font-bold {{ $index < 3 ? 'text-white bg-green-500 rounded-full shadow-sm' : 'text-gray-400' }}">{{ $index + 1 }}</span>
                            <img src="{{ $item->product->image ? asset($item->product->image) : 'https://placehold.co/100x100/e0e0e0/757575?text=IMG' }}"
                                class="w-10 h-10 rounded-lg object-cover border border-gray-100 group-hover:border-green-400 transition">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate group-hover:text-green-600 transition">
                                    {{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->total_sold }} Terjual</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <p class="text-xs">Belum ada data penjualan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RECENT ORDERS TABLE --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
                <a href="{{ route('seller.orders.index') }}"
                    class="text-xs font-bold text-green-600 hover:text-green-800 bg-green-50 px-4 py-2 rounded-xl transition hover:bg-green-100">LIHAT
                    SEMUA</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-white text-gray-500 font-bold text-xs uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4">Invoice</th>
                            <th class="px-8 py-4">Pembeli</th>
                            <th class="px-8 py-4 text-right">Total</th>
                            <th class="px-8 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-green-50/30 transition">
                                <td class="px-8 py-4">
                                    <span
                                        class="font-mono text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded border border-green-200">#{{ $order->order_code }}</span>
                                    <div class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 font-medium text-gray-700">{{ $order->user->name }}</td>
                                <td class="px-8 py-4 font-bold text-gray-900 text-right">
                                    Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide
                                {{ $order->status == 'completed'
                                    ? 'bg-green-100 text-green-700'
                                    : ($order->status == 'paid'
                                        ? 'bg-blue-100 text-blue-700'
                                        : ($order->status == 'shipped'
                                            ? 'bg-purple-100 text-purple-700'
                                            : 'bg-gray-100 text-gray-700')) }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-400 italic">Belum ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function chartComponent() {
            return {
                mode: 'weekly', // 'weekly' atau 'monthly'
                chartInstance: null,

                // Data dari Controller
                data: {
                    weekly: {
                        labels: @json($weekLabels),
                        data: @json($weekData)
                    },
                    monthly: {
                        labels: @json($monthLabels),
                        data: @json($monthData)
                    }
                },

                initChart() {
                    this.renderChart();
                },

                updateChart() {
                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }
                    this.renderChart();
                },

                renderChart() {
                    const ctx = document.getElementById('revenueChart');
                    if (!ctx) return;

                    const currentLabels = this.mode === 'weekly' ? this.data.weekly.labels : this.data.monthly.labels;
                    const currentData = this.mode === 'weekly' ? this.data.weekly.data : this.data.monthly.data;

                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                    this.chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: currentLabels,
                            datasets: [{
                                label: 'Pendapatan',
                                data: currentData,
                                borderColor: '#10b981',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#10b981',
                                pointBorderWidth: 2,
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
                                    grid: {
                                        borderDash: [4, 4],
                                        color: '#f3f4f6'
                                    },
                                    ticks: {
                                        font: {
                                            family: "'Inter', sans-serif",
                                            size: 10
                                        },
                                        callback: (val) => 'Rp ' + (val / 1000) + 'k'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            family: "'Inter', sans-serif",
                                            size: 10
                                        }
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                        }
                    });
                }
            }
        }
    </script>

    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 10px;
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endsection
