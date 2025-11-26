@extends('layouts.seller')
@section('title', 'Dashboard Toko')

@section('content')

<div x-data="{
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    }
}" class="space-y-6 font-inter">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-gray-500 mb-1">
                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-xs font-bold uppercase tracking-wider text-gray-600">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Halo, {{ Auth::user()->seller->name }} 👋</h1>
        </div>
        
        <a href="{{ route('seller.products.index') }}" class="group flex items-center px-5 py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition shadow-lg transform hover:-translate-y-0.5">
            <div class="mr-2 p-1 bg-white/20 rounded-lg group-hover:bg-white/30 transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            Upload Produk
        </a>
    </div>

    @if($ordersToShip > 0)
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 p-6 shadow-xl text-white animate-pulse-slow ring-4 ring-purple-50">
        <div class="absolute right-0 top-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="p-3 bg-white/20 backdrop-blur-md rounded-2xl shadow-inner">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-white">Tugas Pending: {{ $ordersToShip }} Pesanan</h3>
                    <p class="text-indigo-100 text-sm mt-1 font-medium">Pembeli sudah membayar. Segera proses pengiriman.</p>
                </div>
            </div>
            <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}" class="px-6 py-3 bg-white text-indigo-700 font-bold rounded-xl hover:bg-indigo-50 transition shadow-md whitespace-nowrap">
                Kirim Sekarang &rarr;
            </a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="relative bg-gradient-to-br from-green-600 to-emerald-800 p-6 rounded-3xl shadow-lg shadow-green-100 text-white overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 group-hover:bg-white/20 transition"></div>
            
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start">
                    <div class="p-2 bg-white/20 backdrop-blur-md rounded-xl border border-white/10">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold bg-black/20 px-2 py-1 rounded-lg border border-white/10 backdrop-blur-sm">NETTO</span>
                </div>
                <div class="mt-6">
                    <p class="text-green-100 text-sm font-medium opacity-90">Pendapatan Bersih</p>
                    <h2 class="text-3xl font-black tracking-tight mt-1" x-text="formatCurrency({{ $totalRevenue }})"></h2>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Sukses</span>
                </div>
                
                <p class="text-sm text-gray-500 font-medium">Rasio Pesanan Selesai</p>
                
                <div class="flex items-end gap-1.5 mt-1">
                    <h3 class="text-3xl font-black text-gray-900">{{ $completedOrders }}</h3>
                    <span class="text-sm text-gray-400 font-bold mb-1.5">/ {{ $totalOrders }} Total</span>
                </div>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-2.5 mt-4 overflow-hidden">
                <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-1000 ease-out relative" 
                     style="width: {{ $totalOrders > 0 ? ($completedOrders/$totalOrders)*100 : 0 }}%">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-orange-50 text-orange-600 rounded-xl group-hover:bg-orange-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">Live</span>
                </div>

                <p class="text-sm text-gray-500 font-medium">Kesehatan Etalase</p>
                
                <div class="flex items-end gap-1.5 mt-1">
                    <h3 class="text-3xl font-black text-gray-900">{{ $activeProducts }}</h3>
                    <span class="text-sm text-gray-400 font-bold mb-1.5">/ {{ $totalProducts }} Produk</span>
                </div>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-2.5 mt-4 overflow-hidden">
                <div class="bg-orange-500 h-2.5 rounded-full transition-all duration-1000 ease-out" 
                     style="width: {{ $totalProducts > 0 ? ($activeProducts/$totalProducts)*100 : 0 }}%">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl group-hover:bg-yellow-500 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                </div>
                <p class="text-sm text-gray-500 font-medium">Rating Toko</p>
                <div class="flex items-center gap-2 mt-1">
                    <h3 class="text-3xl font-black text-gray-900">{{ number_format($ratingAvg, 1) }}</h3>
                    <div class="flex text-yellow-400 text-sm">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($ratingAvg) ? 'fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <span class="w-2 h-6 bg-green-500 rounded-full mr-3"></span>
                    Tren Pendapatan (7 Hari)
                </h3>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-6 border-b border-gray-100 pb-4">Produk Terlaris</h3>
            
            <div class="space-y-4 flex-1 overflow-y-auto custom-scroll pr-2">
                @forelse($topProducts as $index => $item)
                    <div class="flex items-center gap-3 group cursor-pointer hover:bg-gray-50 p-2 rounded-xl transition">
                        <span class="flex items-center justify-center w-6 h-6 text-xs font-bold {{ $index < 3 ? 'text-white bg-green-500 rounded-full' : 'text-gray-400' }}">
                            {{ $index + 1 }}
                        </span>
                        <img src="{{ asset($item->product->image) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 group-hover:border-green-400 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate group-hover:text-green-600 transition">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->total_sold }} Terjual</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-xs">Belum ada data penjualan.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="w-2 h-6 bg-blue-500 rounded-full mr-3"></span>
                Pesanan Terbaru
            </h3>
            <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline uppercase tracking-wide">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-8 py-4">Invoice</th>
                        <th class="px-8 py-4">Pembeli</th>
                        <th class="px-8 py-4 text-right">Total</th>
                        <th class="px-8 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-8 py-4">
                            <span class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200">#{{ $order->order_code }}</span>
                            <div class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-8 py-4 font-medium text-gray-700">{{ $order->user->name }}</td>
                        <td class="px-8 py-4 font-bold text-gray-900 text-right" x-text="formatCurrency({{ $order->total_amount }})"></td>
                        <td class="px-8 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                  ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                  ($order->status == 'paid' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700')) }}">
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
    Chart.defaults.color = '#9ca3af';

    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pendapatan',
                data: @json($chartData),
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
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { callback: (val) => 'Rp ' + (val/1000) + 'k' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>

<style>
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 10px; }
</style>
@endsection