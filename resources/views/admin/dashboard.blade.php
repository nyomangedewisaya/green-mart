@extends('layouts.admin')
@section('title', 'Dashboard Overview')

@section('content')

<div x-data="{
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    }
}" class="space-y-8 font-inter">

    <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-green-600 via-emerald-600 to-teal-600 p-8 shadow-lg text-white">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2 opacity-80">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="text-sm font-medium uppercase tracking-wider">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
                <p class="mt-2 text-green-50 text-lg max-w-xl">Laporan aktivitas toko Anda hari ini siap. Performa penjualan terlihat stabil.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-5 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl backdrop-blur-sm transition transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Kelola Produk
                </a>
                <a href="{{ route('admin.verification.index') }}" class="flex items-center px-5 py-3 bg-white text-green-700 font-bold rounded-xl shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-1">
                    Verifikasi Seller
                    @if($pendingSellers > 0)
                        <span class="ml-2 flex h-3 w-3 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    @endif
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute top-10 right-40 w-32 h-32 bg-yellow-400/20 rounded-full blur-2xl"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="flex items-center text-xs font-bold px-2.5 py-1 rounded-lg {{ $revenueGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs($revenueGrowth) }}%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1 tracking-tight" x-text="formatCurrency({{ $currentRevenue }})"></h3>
                <p class="text-xs text-gray-400 mt-1">Dibanding bulan lalu</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <span class="flex items-center text-xs font-bold px-2.5 py-1 rounded-lg {{ $orderGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $orderGrowth >= 0 ? '↑' : '↓' }} {{ abs($orderGrowth) }}%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Pesanan</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1 tracking-tight">{{ number_format($currentOrders) }}</h3>
                <p class="text-xs text-gray-400 mt-1">Dibanding bulan lalu</p>
            </div>
        </div>

        <div class="bg-linear-to-br from-gray-900 to-gray-800 p-6 rounded-2xl text-white shadow-lg hover:shadow-xl transition-all duration-300 relative overflow-hidden group lg:col-span-2">
            <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-white/10 transition"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-gray-400 text-sm font-medium uppercase tracking-wider mb-1">Perlu Tindakan</p>
                    <h3 class="text-3xl font-bold text-white">{{ $pendingSellers + $pendingReports + $pendingOrders }} <span class="text-lg font-normal text-gray-400">Item</span></h3>
                </div>
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-md">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-4 relative z-10">
                <div class="text-center p-2 rounded-lg bg-white/5 hover:bg-white/10 transition">
                    <span class="block text-xl font-bold {{ $pendingSellers > 0 ? 'text-red-400' : 'text-gray-400' }}">{{ $pendingSellers }}</span>
                    <span class="text-xs text-gray-400">Verifikasi</span>
                </div>
                <div class="text-center p-2 rounded-lg bg-white/5 hover:bg-white/10 transition">
                    <span class="block text-xl font-bold {{ $pendingReports > 0 ? 'text-red-400' : 'text-gray-400' }}">{{ $pendingReports }}</span>
                    <span class="text-xs text-gray-400">Laporan</span>
                </div>
                <div class="text-center p-2 rounded-lg bg-white/5 hover:bg-white/10 transition">
                    <span class="block text-xl font-bold {{ $pendingOrders > 0 ? 'text-yellow-400' : 'text-gray-400' }}">{{ $pendingOrders }}</span>
                    <span class="text-xs text-gray-400">Unpaid</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1 h-6 bg-green-500 rounded mr-3"></span>
                Analisis Pendapatan
            </h3>
            <div class="relative h-80 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1 h-6 bg-blue-500 rounded mr-3"></span>
                Status Pesanan
            </h3>
            <div class="relative h-60 w-full flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-800 flex items-center">
                <span class="w-1 h-6 bg-purple-500 rounded mr-3"></span>
                Transaksi Terbaru
            </h3>
            <a href="{{ route('admin.transactions.index') }}" class="text-xs font-bold text-green-600 hover:text-green-700 hover:underline uppercase tracking-wide">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200">#{{ $order->order_code }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $order->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900 text-right" x-text="formatCurrency({{ $order->total_amount }})"></td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                  ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 
                                  ($order->status == 'cancelled' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200')) }}">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-gray-400 italic">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <span class="bg-yellow-100 text-yellow-600 p-1.5 rounded-lg mr-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </span>
                    Top Seller (Terlaris)
                </h3>
                <a href="{{ route('admin.sellers.index') }}" class="text-xs font-bold text-green-600 hover:underline">LIHAT SEMUA</a>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topSellers as $index => $seller)
                        <tr class="hover:bg-blue-50/50 transition">
                            <td class="px-6 py-4 flex items-center">
                                <div class="w-8 h-8 shrink-0 flex items-center justify-center font-bold text-sm mr-3 
                                    {{ $index == 0 ? 'bg-yellow-100 text-yellow-700 rounded-full' : 
                                      ($index == 1 ? 'bg-gray-200 text-gray-700 rounded-full' : 
                                      ($index == 2 ? 'bg-orange-100 text-orange-800 rounded-full' : 'text-gray-400')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $seller->name }}</p>
                                    <div class="flex items-center text-xs text-gray-500 mt-0.5">
                                        <svg class="w-3 h-3 text-yellow-400 mr-1 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ $seller->rating ?? '0.0' }} Rating
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-bold text-gray-900">{{ $seller->orders_count }}</p>
                                <p class="text-xs text-gray-500">Transaksi</p>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-6 text-gray-400">Belum ada data seller.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-lg mr-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </span>
                    Produk Terlaris
                </h3>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-green-600 hover:underline">LIHAT SEMUA</a>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topProducts as $item)
                        <tr class="hover:bg-green-50/50 transition">
                            <td class="px-6 py-4 flex items-center">
                                <img src="{{ $item->product->image ? asset($item->product->image) : 'https://placehold.co/100x100/e0e0e0/757575?text=IMG' }}" 
                                     class="w-10 h-10 rounded-lg object-cover border mr-3">
                                <div>
                                    <p class="font-bold text-gray-900 line-clamp-1">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    {{ $item->total_sold }} Terjual
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-6 text-gray-400">Belum ada data penjualan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b7280';
    
    // Revenue
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(22, 163, 74, 0.15)');
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
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#16a34a',
                pointBorderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { callback: (val) => 'Rp ' + (val/1000) + 'k' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Status
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled'],
            datasets: [{
                data: @json($chartStatusData),
                backgroundColor: ['#fbbf24', '#60a5fa', '#a78bfa', '#4ade80', '#f87171'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }
        }
    });
});
</script>

@endsection