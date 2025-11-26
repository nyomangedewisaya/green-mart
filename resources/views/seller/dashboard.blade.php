@extends('layouts.seller')
@section('title', 'Dashboard Toko')

@section('content')

<div x-data="{
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    }
}" class="space-y-8 font-inter">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-gray-500 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium capitalize">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Halo, {{ Auth::user()->seller->name }} 👋</h1>
            <p class="text-gray-500 mt-1">Ringkasan performa toko Anda hari ini.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.products.index') }}" class="group flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:border-green-500 hover:text-green-600 transition shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Produk
            </a>
        </div>
    </div>

    @if($ordersToShip > 0)
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-green-600 to-emerald-600 p-6 shadow-lg text-white shadow-green-100 animate-pulse-slow">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-black/5 rounded-full blur-xl"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="p-3.5 bg-white/20 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Ada {{ $ordersToShip }} Pesanan Perlu Dikirim!</h3>
                    <p class="text-green-50 text-sm mt-1 opacity-90">Pembayaran telah diterima. Segera kemas dan kirim barang ke pelanggan.</p>
                </div>
            </div>
            <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}" class="whitespace-nowrap px-6 py-3 bg-white text-green-700 font-bold rounded-xl hover:bg-green-50 transition shadow-md flex items-center">
                Proses Sekarang <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full -mr-5 -mt-5 group-hover:scale-110 transition duration-500"></div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-green-50 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1 tracking-tight" x-text="formatCurrency({{ $totalRevenue }})"></h3>
                <p class="text-xs text-green-600 font-medium mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Updated
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">{{ $totalOrders > 0 ? round(($completedOrders/$totalOrders)*100) : 0 }}%</span>
            </div>
            <div class="mb-3">
                <p class="text-sm font-medium text-gray-500">Pesanan Selesai</p>
                <div class="flex items-baseline gap-1 mt-1">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $completedOrders }}</h3>
                    <span class="text-sm text-gray-400 font-medium">/ {{ $totalOrders }} Total</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ $totalOrders > 0 ? ($completedOrders/$totalOrders)*100 : 0 }}%"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-orange-50 text-orange-600 rounded-xl group-hover:bg-orange-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">{{ $totalProducts > 0 ? round(($activeProducts/$totalProducts)*100) : 0 }}%</span>
            </div>
            <div class="mb-3">
                <p class="text-sm font-medium text-gray-500">Produk Aktif</p>
                <div class="flex items-baseline gap-1 mt-1">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $activeProducts }}</h3>
                    <span class="text-sm text-gray-400 font-medium">/ {{ $totalProducts }} Total</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ $totalProducts > 0 ? ($activeProducts/$totalProducts)*100 : 0 }}%"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl group-hover:bg-yellow-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500">Rating Toko</p>
            <div class="flex items-center gap-2 mt-1">
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($ratingAvg, 1) }}</h3>
                <div class="flex text-yellow-400 text-sm">
                    @for($i=1; $i<=5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($ratingAvg) ? 'fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Grafik Pendapatan</h3>
                    <p class="text-xs text-gray-500">Performa penjualan 7 hari terakhir</p>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">Last 7 Days</span>
            </div>
            <div class="relative flex-1 min-h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-1 flex flex-col gap-6">
            
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Komposisi Pesanan</h3>
                <div class="relative h-48 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex-1 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Produk Terlaris</h3>
                </div>
                <div class="p-2 space-y-1">
                    @forelse($topProducts as $index => $item)
                        <div class="flex items-center p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer group">
                            <span class="text-xs font-bold text-gray-400 w-6 text-center">{{ $index + 1 }}</span>
                            <img src="{{ asset($item->product->image) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 group-hover:border-green-400 transition">
                            <div class="flex-1 min-w-0 ml-3">
                                <p class="text-sm font-bold text-gray-900 truncate group-hover:text-green-600 transition">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->total_sold }} Terjual</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Pesanan Terbaru</h3>
            <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-green-600 hover:text-green-700 hover:underline uppercase tracking-wide">LIHAT SEMUA</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200">#{{ $order->order_code }}</span>
                            <div class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 text-right" x-text="formatCurrency({{ $order->total_amount }})"></td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                  ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 
                                  ($order->status == 'paid' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-700 border border-gray-200')) }}">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-8 text-gray-400 italic">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    
    // Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(22, 163, 74, 0.15)');
    gradient.addColorStop(1, 'rgba(22, 163, 74, 0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pendapatan',
                data: @json($chartData),
                borderColor: '#16a34a', // Green-600
                backgroundColor: gradient,
                borderWidth: 2,
                pointRadius: 3,
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

    // Status Doughnut Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Belum Bayar', 'Siap Kirim', 'Dikirim', 'Selesai', 'Batal'],
            datasets: [{
                data: @json($chartStatusData),
                backgroundColor: ['#facc15', '#3b82f6', '#a855f7', '#22c55e', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>

@endsection