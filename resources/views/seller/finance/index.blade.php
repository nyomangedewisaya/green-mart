@extends('layouts.seller')
@section('title', 'Keuangan Toko')

@section('content')

    <div x-data="{
        withdrawModal: false,
        amount: '',
        transactionModal: false,
        activeTrx: null,
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        formatDate(dateString) {
            if (!dateString) return '-';
    
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
            const hasTime = dateString.includes('T') || dateString.includes(':');
    
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                ...(hasTime && { hour: '2-digit', minute: '2-digit' })
            });
        },
    
        setMaxAmount(balance) {
            this.amount = balance;
        },
    
        openTrxModal(trx) {
            this.activeTrx = trx;
            this.transactionModal = true;
        },
    
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
                        <h3 class="text-lg font-bold text-gray-800">Arus Kas (Cashflow)</h3>
                        <p class="text-xs text-gray-500 mt-1">Pemasukan vs Pengeluaran 7 hari terakhir</p>
                    </div>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>

            <div
                class="bg-linear-to-br from-gray-800 to-green-600 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden flex flex-col justify-between h-full min-h-[300px] group">
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
                            class="text-[10px] font-bold uppercase tracking-widest text-gray-100 border border-gray-300 px-2 py-1 rounded">Saldo
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
                            <tr @click="openTrxModal({{ json_encode($trx) }})"
                                class="hover:bg-gray-50 transition group items-start cursor-pointer border-l-4 border-transparent {{ $trx->type == 'income' ? 'hover:border-green-400' : 'hover:border-red-400' }}">

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
                                            @if ($trx->description == 'promosi')
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="p-2 rounded-lg bg-red-50 text-red-600 mr-2 shrink-0 border border-red-100">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-sm">Biaya Iklan</p>
                                                        <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[200px]"
                                                            title="{{ $trx->note }}">"{{ $trx->note }}"</p>
                                                    </div>
                                                </div>
                                            @else
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
                                                        class="mt-1 bg-red-50 border-l-2 border-red-500 p-2 rounded-r-md shadow-sm">
                                                        <p class="text-xs text-red-700 leading-relaxed">Note:
                                                            {{ $trx->note }}</p>
                                                    </div>
                                                @endif
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
                                    @if ($trx->description == 'promosi')
                                        @if ($trx->status == 'pending')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 uppercase">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                                Review
                                            </span>
                                        @elseif($trx->status == 'paid')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-1.5"></span> Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase">
                                                Selesai
                                            </span>
                                        @endif
                                    @elseif($trx->type == 'expense')
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

                <div class="bg-green-600 px-6 py-5 flex justify-between items-center text-white shadow-md z-10">
                    <h3 class="text-lg font-bold">Tarik Dana</h3>
                    <button @click="withdrawModal = false"
                        class="text-white hover:text-gray-300 transition"><svg
                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form action="{{ route('seller.finance.withdraw') }}" method="POST"
                    class="p-6 space-y-5 overflow-y-auto custom-scroll" x-data="{
                        useSaved: {{ $savedBanks->count() > 0 ? 'true' : 'false' }},
                        selectedBankId: '',
                        bankName: '',
                        accNum: '',
                        accHolder: '',
                    
                        fillBank(el) {
                            const option = el.options[el.selectedIndex];
                            if (option.value) {
                                this.bankName = option.dataset.bank;
                                this.accNum = option.dataset.num;
                                this.accHolder = option.dataset.holder;
                            } else {
                                this.bankName = '';
                                this.accNum = '';
                                this.accHolder = '';
                            }
                        }
                    }">
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
                        <p class="text-xs text-gray-400 mt-1 ml-1">Minimal Rp 10.000</p>
                    </div>

                    <hr class="border-gray-100">

                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-xs font-bold text-gray-500 uppercase">Tujuan Transfer</label>

                            @if ($savedBanks->count() > 0)
                                <button type="button" @click="useSaved = !useSaved"
                                    class="text-xs text-green-600 hover:underline font-bold transition">
                                    <span x-text="useSaved ? '+ Input Baru' : 'Pilih Tersimpan'"></span>
                                </button>
                            @endif
                        </div>

                        @if ($savedBanks->count() > 0)
                            <div x-show="useSaved" class="space-y-4">
                                <div class="relative">
                                    <select x-model="selectedBankId" @change="fillBank($event.target)"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm bg-white appearance-none cursor-pointer">
                                        <option value="" disabled selected>-- Pilih Rekening --</option>
                                        @foreach ($savedBanks as $bank)
                                            <option value="{{ $bank->id }}" data-bank="{{ $bank->bank_name }}"
                                                data-num="{{ $bank->account_number }}"
                                                data-holder="{{ $bank->account_holder }}">
                                                {{ $bank->bank_name }} - {{ $bank->account_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <div x-show="selectedBankId"
                                    class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm space-y-2 shadow-sm transition"
                                    x-transition>
                                    <div class="flex justify-between"><span
                                            class="text-gray-500 text-xs uppercase font-bold">Bank</span> <span
                                            class="font-bold text-gray-800" x-text="bankName"></span></div>
                                    <div class="flex justify-between"><span
                                            class="text-gray-500 text-xs uppercase font-bold">No. Rek</span> <span
                                            class="font-mono font-bold text-gray-800 tracking-wide"
                                            x-text="accNum"></span></div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2 mt-1"><span
                                            class="text-gray-500 text-xs uppercase font-bold">Atas Nama</span> <span
                                            class="font-medium text-gray-800 uppercase" x-text="accHolder"></span></div>

                                    <div class="text-right pt-2">
                                        <button type="button"
                                            @click="if(confirm('Hapus rekening ini?')) document.getElementById('delete-bank-'+selectedBankId).submit()"
                                            class="text-xs text-red-500 hover:text-red-700 hover:underline font-medium flex items-center justify-end w-full gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Rekening Ini
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div x-show="!useSaved || {{ $savedBanks->count() == 0 ? 'true' : 'false' }}" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Nama
                                    Bank</label>
                                <input type="text" name="bank_name" x-model="bankName" :required="!useSaved"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm transition placeholder-gray-300"
                                    placeholder="Contoh: BCA, Mandiri">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">No.
                                        Rekening</label>
                                    <input type="number" name="account_number" x-model="accNum" :required="!useSaved"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm transition placeholder-gray-300"
                                        placeholder="123xxxxx">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Atas
                                        Nama</label>
                                    <input type="text" name="account_holder" x-model="accHolder"
                                        :required="!useSaved"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm transition placeholder-gray-300"
                                        placeholder="Nama Pemilik">
                                </div>
                            </div>

                            <label
                                class="flex items-center space-x-3 cursor-pointer mt-2 bg-gray-50 p-3 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                <input type="checkbox" name="save_bank" value="1"
                                    class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                                <span class="text-xs text-gray-600 font-medium">Simpan rekening ini untuk penarikan
                                    berikutnya?</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Kirim Permintaan
                        </button>
                    </div>
                </form>

                <div class="hidden">
                    @foreach ($savedBanks as $bank)
                        <form id="delete-bank-{{ $bank->id }}"
                            action="{{ route('seller.finance.bank.destroy', $bank->id) }}" method="POST">
                            @csrf @method('DELETE')
                        </form>
                    @endforeach
                </div>

            </div>
        </div>


        <div x-show="transactionModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="transactionModal" @click="transactionModal = false"
                class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <div x-show="transactionModal"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4">
                    <div class="px-6 py-6 text-white relative overflow-hidden"
                        :class="activeTrx?.type == 'income' ? 'bg-gradient-to-br from-green-600 to-green-700' :
                            'bg-gradient-to-br from-red-600 to-red-700'">

                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>

                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">Total Nominal</p>
                                <h2 class="text-3xl font-bold tracking-tight"
                                    x-text="(activeTrx?.type == 'income' ? '+ ' : '- ') + formatCurrency(activeTrx?.amount)">
                                </h2>
                            </div>
                            <button @click="transactionModal = false"
                                class="bg-white/20 hover:bg-white/30 text-white p-1.5 rounded-full transition backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="relative z-10 mt-4 flex items-center gap-2">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-white/20 backdrop-blur-md border border-white/10 shadow-sm">
                                <span
                                    x-text="activeTrx?.description == 'promosi' ? (activeTrx?.status == 'paid' ? 'Aktif' : 'Menunggu') : (activeTrx?.type == 'income' ? 'Selesai' : (activeTrx?.description == 'approved' ? 'Berhasil' : activeTrx?.description))"></span>
                            </span>
                            <span class="text-xs opacity-80 font-mono" x-text="formatDate(activeTrx?.date)"></span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">

                        <template x-if="activeTrx?.type == 'income'">
                            <div class="space-y-5">
                                <div
                                    class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-100">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg text-green-600 shadow-sm"><svg class="w-5 h-5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg></div>
                                        <div>
                                            <p class="text-xs font-bold text-green-800 uppercase">Sumber Dana</p>
                                            <p class="font-bold text-gray-900">Penjualan Produk</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-4">
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">No. Invoice</p>
                                        <p class="font-mono font-bold text-gray-800 select-all"
                                            x-text="'#' + activeTrx?.code"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Metode Pembayaran</p>
                                        <p class="font-medium text-gray-800 uppercase"
                                            x-text="activeTrx?.payment_method || 'Transfer Bank'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="activeTrx?.description == 'promosi'">
                            <div class="space-y-5">
                                <div
                                    class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg text-red-600 shadow-sm"><svg class="w-5 h-5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                            </svg></div>
                                        <div>
                                            <p class="text-xs font-bold text-red-800 uppercase">Tipe Transaksi</p>
                                            <p class="font-bold text-gray-900">Pembayaran Iklan</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Judul Iklan</p>
                                        <p class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100"
                                            x-text="activeTrx?.note"></p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 pt-2">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Mulai Tayang</p>
                                            <p class="font-medium text-sm text-gray-800" x-text="formatDate(activeTrx?.start_date)">
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Selesai Tayang</p>
                                            <p class="font-medium text-sm text-gray-800" x-text="formatDate(activeTrx?.end_date)">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template
                            x-if="activeTrx?.description == 'pending' || activeTrx?.description == 'approved' || activeTrx?.description == 'rejected'">
                            <div class="space-y-5">
                                <div
                                    class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg text-red-600 shadow-sm"><svg class="w-5 h-5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg></div>
                                        <div>
                                            <p class="text-xs font-bold text-red-800 uppercase">Tipe Transaksi</p>
                                            <p class="font-bold text-gray-900">Penarikan Saldo</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-3 shadow-sm">
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                        <span class="text-xs text-gray-500 uppercase font-bold">Bank Tujuan</span>
                                        <span class="font-bold text-gray-900" x-text="activeTrx?.bank_name"></span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                        <span class="text-xs text-gray-500 uppercase font-bold">No. Rekening</span>
                                        <span class="font-mono font-medium text-gray-800 select-all"
                                            x-text="activeTrx?.account_number"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 uppercase font-bold">Atas Nama</span>
                                        <span class="font-medium text-gray-800 uppercase"
                                            x-text="activeTrx?.account_holder"></span>
                                    </div>
                                </div>

                                <template x-if="activeTrx?.description == 'rejected' && activeTrx?.note">
                                    <div class="bg-red-50 border border-red-200 p-3 rounded-xl text-sm">
                                        <p class="font-bold text-red-800 text-xs uppercase mb-1">Alasan Penolakan:</p>
                                        <p class="text-red-600 leading-relaxed">"<span x-text="activeTrx?.note"></span>"
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-center">
                        <button @click="transactionModal = false"
                            class="text-sm font-bold text-gray-500 hover:text-gray-800 transition">Tutup</button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-center">
                <button @click="transactionModal = false"
                    class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">Tutup Detail</button>
            </div>
        </div>
    </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Inter', sans-serif";

            const ctx = document.getElementById('financeChart').getContext('2d');

            // Gradient Hijau (Pemasukan)
            const gradientIncome = ctx.createLinearGradient(0, 0, 0, 300);
            gradientIncome.addColorStop(0, 'rgba(22, 163, 74, 0.2)'); // Green-600
            gradientIncome.addColorStop(1, 'rgba(22, 163, 74, 0)');

            // Gradient Merah (Pengeluaran)
            const gradientExpense = ctx.createLinearGradient(0, 0, 0, 300);
            gradientExpense.addColorStop(0, 'rgba(220, 38, 38, 0.2)'); // Red-600
            gradientExpense.addColorStop(1, 'rgba(220, 38, 38, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                            label: 'Pemasukan',
                            data: @json($chartIncome),
                            borderColor: '#16a34a', // Green
                            backgroundColor: gradientIncome,
                            borderWidth: 3,
                            pointRadius: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#16a34a',
                            pointBorderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($chartExpense),
                            borderColor: '#dc2626', // Red
                            backgroundColor: gradientExpense,
                            borderWidth: 3,
                            pointRadius: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#dc2626',
                            pointBorderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true, // Tampilkan Legend
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            bodySpacing: 6,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat(
                                        'id-ID').format(context.parsed.y);
                                }
                            }
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
                                padding: 10,
                                callback: (val) => val >= 1000 ? 'Rp ' + (val / 1000) + 'k' : val
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                padding: 10
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
