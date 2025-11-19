@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<div x-data="{
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    }
}" class="space-y-8">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                Halo, {{ Auth::user()->name }} 👋
            </h1>
            <p class="text-gray-500 mt-1">Pantau performa <span class="text-green-600 font-semibold">Green Mart</span> secara real-time.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Manage Produk
            </a>
            <a href="{{ route('admin.verification.index') }}" class="flex items-center px-4 py-2.5 bg-gray-900 text-white border border-gray-900 rounded-xl text-sm font-medium hover:bg-gray-800 transition shadow-lg shadow-gray-200">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Verifikasi Seller
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-lg transition duration-300 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-green-50 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                @if($revenueGrowth >= 0)
                    <span class="flex items-center text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded-lg">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        {{ $revenueGrowth }}%
                    </span>
                @else
                    <span class="flex items-center text-xs font-bold text-red-700 bg-red-100 px-2 py-1 rounded-lg">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
                        {{ $revenueGrowth }}%
                    </span>
                @endif
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1 tracking-tight" x-text="formatCurrency({{ $currentRevenue }})"></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-lg transition duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <span class="flex items-center text-xs font-bold {{ $orderGrowth >= 0 ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} px-2 py-1 rounded-lg">
                    {{ $orderGrowth }}%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Pesanan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1 tracking-tight">{{ number_format($currentOrders) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-lg transition duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <span class="text-xs font-bold text-purple-700 bg-purple-100 px-2 py-1 rounded-lg">Bulan Ini</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pengguna Baru</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1 tracking-tight">{{ number_format($newUsersCount) }}</h3>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-5 rounded-2xl border border-gray-800 shadow-lg hover:shadow-xl transition duration-300 group relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-5 rounded-bl-full -mr-8 -mt-8"></div>
            
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="p-2.5 bg-white/10 rounded-xl text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
                <span class="flex items-center text-xs font-bold bg-red-500 text-white px-2 py-1 rounded-lg animate-pulse">
                    {{ $pendingSellers + $pendingReports + $pendingOrders }} Aktif
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-400">Total Tugas Pending</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-white tracking-tight">{{ $pendingSellers + $pendingReports + $pendingOrders }}</h3>
                    <span class="text-xs text-gray-400 mb-1">items</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Analisis Pendapatan</h3>
                        <p class="text-sm text-gray-500">Performa penjualan 7 hari terakhir</p>
                    </div>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Transaksi Terbaru</h3>
                    <a href="{{ route('admin.transactions.index') }}" class="text-sm font-medium text-green-600 hover:text-green-700 transition">Lihat Semua &rarr;</a>
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
                            <tr class="hover:bg-gray-50/80 transition group">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200 group-hover:border-green-200 group-hover:bg-green-50 group-hover:text-green-700 transition">#{{ $order->order_code }}</span>
                                    <div class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->seller->name ?? 'Toko Hapus' }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900 text-right" x-text="formatCurrency({{ $order->total_amount }})"></td>
                                <td class="px-6 py-4 text-center">
                                    @if($order->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            ⏳ Pending
                                        </span>
                                    @elseif($order->status == 'paid')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            💳 Paid
                                        </span>
                                    @elseif($order->status == 'shipped')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                            🚚 Shipped
                                        </span>
                                    @elseif($order->status == 'completed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                            ✅ Completed
                                        </span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                            ❌ Cancelled
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-8 text-gray-400">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Status Pesanan</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Top Seller
                    </h3>
                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Bulan Ini</span>
                </div>
                
                <div class="p-4 space-y-3">
                    @forelse($topSellers as $index => $seller)
                        @php
                            // Hitung persentase relatif terhadap seller #1 untuk progress bar
                            $maxOrders = $topSellers->first()->orders_count ?? 1;
                            $percentage = ($seller->orders_count / $maxOrders) * 100;
                        @endphp

                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition group border border-transparent hover:border-gray-200">
                            <div class="flex items-center w-full">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center mr-3">
                                    @if($index == 0)
                                        <span class="text-2xl">🥇</span>
                                    @elseif($index == 1)
                                        <span class="text-2xl">🥈</span>
                                    @elseif($index == 2)
                                        <span class="text-2xl">🥉</span>
                                    @else
                                        <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 font-bold text-xs flex items-center justify-center">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                
                                <div class="flex-1 min-w-0 mr-4">
                                    <div class="flex justify-between mb-1">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $seller->name }}</p>
                                        <p class="text-xs font-bold text-green-600">{{ $seller->orders_count }} Sold</p>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-sm">Belum ada data penjualan.</div>
                    @endforelse
                    
                    <a href="{{ route('admin.sellers.index') }}" class="block text-center text-xs font-bold text-gray-500 hover:text-green-600 mt-2 pt-2 border-t border-gray-100 transition">LIHAT SEMUA SELLER</a>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b7280';
    Chart.defaults.scale.grid.color = '#f3f4f6';

    // 1. Revenue Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(22, 163, 74, 0.2)');
    gradient.addColorStop(1, 'rgba(22, 163, 74, 0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($revenueLabels),
            datasets: [{
                label: 'Pendapatan',
                data: @json($revenueData),
                borderColor: '#16a34a', 
                backgroundColor: gradient,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#16a34a',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 13 },
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { borderDash: [4, 4], drawTicks: false },
                    ticks: { padding: 10, callback: (val) => val >= 1000 ? (val/1000) + 'k' : val }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { padding: 10 }
                }
            }
        }
    });

    // 2. Status Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled'],
            datasets: [{
                data: @json($chartStatusData),
                backgroundColor: ['#facc15', '#60a5fa', '#c084fc', '#4ade80', '#f87171'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 11 } }
                }
            }
        }
    });
});
</script>

@endsection