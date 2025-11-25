@extends('layouts.seller')
@section('title', 'Keuangan Toko')

@section('content')

    <div x-data="{
        withdrawModal: false,
        amount: '',
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        setMaxAmount(balance) {
            this.amount = balance;
        }
    }" class="space-y-6 font-inter">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Analisis Keuangan</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau arus kas dan performa pendapatan toko Anda.</p>
            </div>
            <button @click="withdrawModal = true"
                class="group flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2 text-green-200 group-hover:text-white transition" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Tarik Saldo
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Tren Pemasukan</h3>
                        <p class="text-xs text-gray-500 mt-1">Grafik pendapatan bersih 7 hari terakhir</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Bulan Ini</p>
                        <div class="flex items-center justify-end gap-2">
                            <span
                                class="text-xl font-bold text-gray-900">{{ number_format($thisMonthRevenue, 0, ',', '.') }}</span>
                            @if ($growthPercentage != 0)
                                <span
                                    class="text-xs font-bold px-1.5 py-0.5 rounded {{ $growthPercentage > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $growthPercentage > 0 ? '+' : '' }}{{ round($growthPercentage, 1) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>

            <div
                class="bg-linear-to-br from-green-600 to-gray-800 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden flex flex-col justify-between h-full min-h-[300px] group">
                <div
                    class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full blur-3xl -mr-10 -mt-10 group-hover:bg-white/10 transition duration-700">
                </div>

                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md border border-white/10">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-gray-400 border border-gray-700 px-2 py-1 rounded">Saldo
                            Aktif</span>
                    </div>

                    <div>
                        <p class="text-gray-400 text-xs uppercase font-medium mb-1">Total Tersedia</p>
                        <h2 class="text-4xl font-bold tracking-tight text-white"
                            x-text="formatCurrency({{ $currentBalance }})"></h2>
                    </div>
                </div>

                <div class="relative z-10 mt-6 space-y-3 pt-6 border-t border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Sedang Diproses</span>
                        <span class="font-bold text-yellow-400" x-text="formatCurrency({{ $pendingWithdrawal }})"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Total Ditarik</span>
                        <span class="font-bold text-gray-200" x-text="formatCurrency({{ $totalWithdrawn }})"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center flex-wrap gap-3">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Mutasi Rekening
                </h3>

                <div class="flex bg-gray-100 p-1 rounded-lg">
                    <a href="{{ route('seller.finance.index', ['type' => 'all']) }}"
                        class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterType == 'all' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('seller.finance.index', ['type' => 'income']) }}"
                        class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterType == 'income' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Masuk
                    </a>
                    <a href="{{ route('seller.finance.index', ['type' => 'expense']) }}"
                        class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterType == 'expense' ? 'bg-white text-red-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Keluar
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50 transition group items-start">
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap font-medium align-top">
                                    {{ \Carbon\Carbon::parse($trx->date)->locale('id')->translatedFormat('d M Y, H:i') }}
                                </td>

                                <td class="px-6 py-4 align-top">
                                    @if ($trx->type == 'income')
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="p-2 rounded-lg bg-green-50 text-green-600 mr-2 shrink-0 border border-green-100 mt-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-sm">Penjualan Produk</p>
                                                <p class="text-xs text-green-600 font-mono mt-0.5">Order
                                                    #{{ $trx->code }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="p-2 rounded-lg bg-red-50 text-red-600 mr-2 shrink-0 border border-red-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 text-sm">Penarikan Dana</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">Transfer Bank</p>
                                                </div>
                                            </div>

                                            @if ($trx->description == 'rejected' && !empty($trx->note))
                                                <div
                                                    class="mt-1 bg-red-50 border-l-2 border-red-500 p-3 rounded-r-md shadow-sm">
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>
                                                            <p
                                                                class="text-[10px] font-bold text-red-800 uppercase tracking-wide mb-0.5">
                                                                Alasan Penolakan:</p>
                                                            <p class="text-xs text-red-700 leading-relaxed">
                                                                "{{ $trx->note }}"</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 align-top">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide 
                {{ $trx->type == 'income' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $trx->type == 'income' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap align-top">
                                    <span
                                        class="font-bold text-sm {{ $trx->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $trx->type == 'income' ? '+' : '-' }}
                                        {{ number_format($trx->amount, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center align-top">
                                    @if ($trx->type == 'expense')
                                        @if ($trx->description == 'pending')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-200 uppercase">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                                Proses
                                            </span>
                                        @elseif($trx->description == 'approved')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Sukses
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 uppercase">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Gagal
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs font-bold text-gray-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 p-3 rounded-full mb-2">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada riwayat transaksi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="withdrawModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="withdrawModal" @click="withdrawModal = false"
                class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <div x-show="withdrawModal"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-green-600 p-6 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold">Kirim Pesanan</h3>
                    <button @click="withdrawModal = false" class="text-green-200 hover:text-white"><svg class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <form action="{{ route('seller.finance.withdraw') }}" method="POST"
                    class="p-6 space-y-5 overflow-y-auto max-h-[80vh]">
                    @csrf
                    <div class="bg-green-50 p-4 rounded-xl border border-green-100 flex justify-between items-center">
                        <span class="text-sm text-green-800 font-bold">Saldo Tersedia</span>
                        <span class="text-xl font-bold text-green-700"
                            x-text="formatCurrency({{ $currentBalance }})"></span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Nominal Penarikan</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-400 font-bold">Rp</span>
                            <input type="number" name="amount" x-model="amount" required min="10000"
                                max="{{ $currentBalance }}"
                                class="w-full pl-10 pr-16 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none font-bold text-gray-800 text-lg"
                                placeholder="0">
                            <button type="button" @click="setMaxAmount({{ $currentBalance }})"
                                class="absolute right-3 top-2 text-xs bg-green-100 text-green-700 px-2 py-1.5 rounded hover:bg-green-200 font-bold transition">MAX</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 ml-1">Minimal penarikan Rp 10.000</p>
                    </div>
                    <hr class="border-gray-100">
                    <div class="space-y-3">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Bank</label><input
                                type="text" name="bank_name" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm"
                                placeholder="BCA"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label
                                    class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Rekening</label><input
                                    type="number" name="account_number" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm">
                            </div>
                            <div><label
                                    class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Pemilik</label><input
                                    type="text" name="account_holder" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm">
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5">Kirim
                        Permintaan</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Inter', sans-serif";
            const ctx = document.getElementById('financeChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(22, 163, 74, 0.2)');
            gradient.addColorStop(1, 'rgba(22, 163, 74, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Pemasukan',
                        data: @json($chartData),
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
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
        });
    </script>
@endsection
