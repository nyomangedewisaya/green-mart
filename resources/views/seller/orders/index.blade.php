@extends('layouts.seller')
@section('title', 'Pesanan Masuk')

@section('content')

    <div x-data="{
        detailModal: false,
        resiModal: false,
        rejectModal: false,
        modalOrder: null,
        resi: '',
        actionUrl: '',
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
    
        getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'paid': 'bg-blue-100 text-blue-800 border-blue-200',
                'shipped': 'bg-purple-100 text-purple-800 border-purple-200',
                'completed': 'bg-green-100 text-green-800 border-green-200',
                'cancelled': 'bg-red-100 text-red-800 border-red-200'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200';
        },
    
        openResiModal(order) {
            this.modalOrder = order;
            this.resi = '';
            // Menggunakan order_code sesuai route key name
            this.actionUrl = '{{ url('seller/orders') }}/' + order.order_code;
            this.resiModal = true;
        },
    
        openRejectModal(order) {
            this.modalOrder = order;
            // Menggunakan order_code sesuai route key name
            this.actionUrl = '{{ url('seller/orders') }}/' + order.order_code;
            this.rejectModal = true;
        }
    }" class="space-y-6 font-inter">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pesanan Masuk</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola pesanan, input resi, dan pantau pengiriman.</p>
            </div>
        </div>

        <div class="bg-white border-b rounded-md border-gray-200 sticky top-0 z-10 shadow-sm">
            <nav class="flex space-x-6 px-6 overflow-x-auto no-scrollbar" aria-label="Tabs">

                <a href="{{ route('seller.orders.index', ['status' => 'all']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status == 'all' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua
                </a>

                <a href="{{ route('seller.orders.index', ['status' => 'unpaid']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center {{ $status == 'unpaid' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Belum Dibayar
                    @if ($counts['pending'] > 0)
                        <span
                            class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $counts['pending'] }}</span>
                    @endif
                </a>

                <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center {{ $status == 'paid' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Perlu Dikirim
                    @if ($counts['paid'] > 0)
                        <span
                            class="ml-2 bg-red-500 text-white py-0.5 px-2 rounded-full text-xs font-bold shadow-sm">{{ $counts['paid'] }}</span>
                    @endif
                </a>

                <a href="{{ route('seller.orders.index', ['status' => 'shipped']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center {{ $status == 'shipped' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Dikirim
                    @if ($counts['shipped'] > 0)
                        <span
                            class="ml-2 bg-purple-100 text-purple-600 py-0.5 px-2 rounded-full text-xs">{{ $counts['shipped'] }}</span>
                    @endif
                </a>

                <a href="{{ route('seller.orders.index', ['status' => 'completed']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status == 'completed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Selesai
                </a>

                <a href="{{ route('seller.orders.index', ['status' => 'cancelled']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status == 'cancelled' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Dibatalkan
                </a>
            </nav>
        </div>

        <div class="relative max-w-md mt-6">
            <form action="{{ route('seller.orders.index') }}" method="GET">
                <input type="hidden" name="status" value="{{ $status }}">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" placeholder="Cari No. Invoice atau Nama Pembeli..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm shadow-sm">
            </form>
        </div>

        <div class="space-y-4">
            @forelse($orders as $order)
                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden group">

                    <div
                        class="bg-gray-50 px-6 py-3 border-b border-gray-100 flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="font-bold text-gray-900">{{ $order->user->name ?? 'Guest' }}</span>
                            <span class="text-gray-300">|</span>
                            <span
                                class="font-mono text-green-600 font-bold cursor-pointer hover:underline">#{{ $order->order_code }}</span>
                            <span class="text-gray-300">|</span>
                            <span
                                class="text-gray-500 text-xs">{{ $order->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                        </div>

                        <div>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide border"
                                :class="getStatusColor('{{ $order->status }}')">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">

                        <div class="md:col-span-6 flex flex-col justify-center space-y-3">
                            @foreach ($order->details->take(2) as $item)
                                <div
                                    class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition border border-transparent hover:border-gray-100">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <img src="{{ asset($item->product->image ?? 'img/default.png') }}"
                                            class="w-12 h-12 rounded-lg object-cover border border-gray-100 shadow-sm bg-white shrink-0">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-800 line-clamp-1">
                                                {{ $item->product->name ?? 'Produk Dihapus' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp
                                                {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right pl-4 shrink-0">
                                        <p class="text-sm font-bold text-gray-700">Rp
                                            {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if ($order->details->count() > 2)
                                <p class="text-xs text-gray-400 pl-2 italic">+ {{ $order->details->count() - 2 }} produk
                                    lainnya...</p>
                            @endif
                        </div>

                        <div
                            class="md:col-span-3 border-l border-gray-100 pl-0 md:pl-6 flex flex-col justify-center h-full">
                            <p class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wider">Total Tagihan</p>
                            <p class="text-lg font-bold text-green-600">Rp
                                {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <p class="text-xs text-gray-500 uppercase font-medium">{{ $order->payment_method }}</p>
                            </div>
                        </div>

                        <div class="md:col-span-3 flex flex-col gap-2 justify-center">

                            @if ($order->status == 'paid')
                                {{-- PERBAIKAN: Menggunakan Js::from agar data ter-passing dengan aman --}}
                                <button @click="openResiModal({{ \Illuminate\Support\Js::from($order) }})"
                                    class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Kirim Pesanan
                                </button>

                                <button @click="openRejectModal({{ \Illuminate\Support\Js::from($order) }})"
                                    class="w-full py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition">
                                    Tolak Pesanan
                                </button>
                            @endif

                            <button
                                @click="detailModal = true; modalOrder = {{ \Illuminate\Support\Js::from($order->load('details.product', 'user')) }}"
                                class="w-full py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 hover:text-green-600 transition">
                                Lihat Rincian
                            </button>
                        </div>
                    </div>

                    @if ($order->status == 'shipped')
                        <div
                            class="bg-purple-50 px-6 py-3 border-t border-purple-100 flex flex-wrap justify-between items-center gap-4">
                            <div class="flex items-center gap-2 text-sm text-purple-800">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">Sedang Dikirim:</span>
                                <span class="font-bold">{{ $order->shipping_courier }}</span>
                                <span
                                    class="bg-white px-2 py-0.5 rounded border border-purple-200 font-mono text-xs select-all">{{ $order->shipping_resi }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-200 border-dashed">
                    <div class="bg-gray-50 rounded-full p-4 w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-lg">Belum Ada Pesanan</h3>
                    <p class="text-gray-500 text-sm mt-1">Pesanan yang sesuai filter tidak ditemukan.</p>
                </div>
            @endforelse

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>

        {{-- MODAL KIRIM PESANAN --}}
        <div x-show="resiModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;"
            x-cloak>
            <div x-show="resiModal" @click="resiModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="resiModal"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-green-600 p-6 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold">Kirim Pesanan</h3>
                    <button @click="resiModal = false" class="text-green-200 hover:text-white"><svg class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-5">
                    @csrf @method('PUT')
                    <input type="hidden" name="action" value="ship">

                    <div class="text-center mb-2">
                        <p class="text-sm text-gray-500">Masukkan resi untuk pesanan:</p>
                        <p class="text-xl font-bold text-green-600 font-mono mt-1" x-text="'#' + modalOrder?.order_code">
                        </p>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-xl p-4 flex items-center gap-4">
                        <div class="p-3 bg-white rounded-lg text-green-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-green-800 uppercase mb-0.5">Kurir Pilihan Pembeli</p>
                            <h4 class="text-lg font-bold text-gray-800"
                                x-text="modalOrder?.shipping_courier || 'Kurir Toko'"></h4>
                            <p class="text-xs text-gray-500" x-text="modalOrder?.shipping_service || 'Layanan Standar'">
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Nomor Resi <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="shipping_resi" x-model="resi" required autofocus
                                class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition font-mono text-lg uppercase tracking-wider placeholder-gray-300"
                                placeholder="JPxxxxxxxxxx">
                            <div class="absolute right-3 top-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 ml-1">Pastikan resi valid agar bisa dilacak pembeli.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Konfirmasi Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;"
            x-cloak>
            <div x-show="rejectModal" @click="rejectModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="rejectModal"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-red-600 p-6 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold">Tolak Pesanan</h3>
                    <button @click="rejectModal = false" class="text-red-200 hover:text-white"><svg class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-5">
                    @csrf @method('PUT')
                    <input type="hidden" name="action" value="cancel">

                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Anda Yakin?</h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Pesanan <span class="font-mono font-bold text-gray-800"
                                x-text="'#' + modalOrder?.order_code"></span> akan dibatalkan.
                        </p>
                        <p class="text-xs text-red-500 mt-1">Stok produk akan dikembalikan otomatis.</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="rejectModal = false"
                            class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">Batal</button>
                        <button type="submit"
                            class="flex-1 py-2.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-md">Ya,
                            Tolak</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;"
            x-cloak>
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="detailModal"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-green-600 px-6 py-5 flex justify-between items-center shadow-md z-10">
                    <div>
                        <h3 class="text-lg font-bold text-white">Rincian Pesanan</h3>
                        <p class="text-green-100 text-xs mt-0.5 flex items-center gap-2">
                            <span>Invoice: <span class="font-mono font-bold"
                                    x-text="'#' + modalOrder?.order_code"></span></span>
                            <span class="text-green-300">•</span>
                            <span x-text="formatDate(modalOrder?.created_at)"></span>
                        </p>
                    </div>
                    <button @click="detailModal = false" class="text-red-200 hover:text-white"><svg class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <div class="p-6 overflow-y-auto custom-scroll space-y-6 bg-gray-50/50">
                    <div
                        class="flex justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg"
                                :class="{
                                    'bg-yellow-100 text-yellow-600': modalOrder?.status == 'pending',
                                    'bg-blue-100 text-blue-600': modalOrder?.status == 'paid',
                                    'bg-purple-100 text-purple-600': modalOrder?.status == 'shipped',
                                    'bg-green-100 text-green-600': modalOrder?.status == 'completed',
                                    'bg-red-100 text-red-600': modalOrder?.status == 'cancelled'
                                }">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Status Pesanan</p>
                                <p class="font-bold text-gray-800 text-lg capitalize" x-text="modalOrder?.status"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold">Metode Bayar</p>
                            <p class="font-medium text-gray-800 capitalize" x-text="modalOrder?.payment_method"></p>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h4
                            class="text-sm font-bold text-gray-800 flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Info Pengiriman
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Penerima</p>
                                <p class="font-bold text-gray-900" x-text="modalOrder?.user?.name"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Kurir</p>
                                <p class="font-medium text-gray-900" x-text="modalOrder?.shipping_courier || '-'"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p>
                                <p class="text-gray-700 leading-relaxed bg-gray-50 p-2 rounded border border-gray-100"
                                    x-text="modalOrder?.address"></p>
                            </div>
                            <div class="col-span-2" x-show="modalOrder?.shipping_resi">
                                <p class="text-xs text-gray-500 mb-1">Nomor Resi</p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-mono font-bold text-gray-800 bg-yellow-50 px-2 py-1 rounded border border-yellow-200 select-all"
                                        x-text="modalOrder?.shipping_resi"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-100">
                            <h4 class="text-sm font-bold text-gray-800">Item Dibeli</h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <template x-for="item in modalOrder?.details" :key="item.id">
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shrink-0">
                                            <img src="https://placehold.co/100x100/e0e0e0/757575?text=IMG"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900" x-text="item.product?.name"></p>
                                            <p class="text-xs text-gray-500 mt-1"
                                                x-text="item.quantity + ' x ' + formatCurrency(item.price)"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(item.subtotal)">
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="px-6 pb-6" x-data="{ showReport: false }">

                    <div class="flex justify-end" x-show="!showReport">
                        <button @click="showReport = true" type="button"
                            class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Laporkan Pembeli
                        </button>
                    </div>

                    <div x-show="showReport" x-transition class="mt-4 bg-red-50 border border-red-100 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-sm font-bold text-red-800 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Laporkan Masalah
                            </h4>
                            <button @click="showReport = false" type="button" class="text-gray-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('seller.reports.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" :value="modalOrder?.id">

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-red-700 mb-1">Alasan Pelaporan</label>
                                    <select name="reason" required
                                        class="w-full px-3 py-2 bg-white border border-red-200 rounded-lg text-sm focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none text-gray-700">
                                        <option value="">Pilih Masalah...</option>
                                        <option value="Pembeli Tidak Responsif">Pembeli Tidak Responsif</option>
                                        <option value="Menolak Bayar COD">Menolak Bayar COD</option>
                                        <option value="Alamat Palsu / Fiktif">Alamat Palsu / Fiktif</option>
                                        <option value="Chat Kasar / SARA">Chat Kasar / SARA</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-red-700 mb-1">Detail Kejadian</label>
                                    <textarea name="description" rows="2" required
                                        class="w-full px-3 py-2 bg-white border border-red-200 rounded-lg text-sm focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none placeholder-red-400"
                                        placeholder="Jelaskan kronologi masalah dengan singkat..."></textarea>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                        Kirim Laporan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div
                    class="bg-white px-6 py-4 border-t border-gray-200 flex justify-between items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] relative z-10">
                    <span class="text-sm font-medium text-gray-500">Total Pembayaran</span>
                    <span class="text-2xl font-bold text-green-600"
                        x-text="formatCurrency(modalOrder?.total_amount)"></span>
                </div>
            </div>
        </div>

    </div>
@endsection
