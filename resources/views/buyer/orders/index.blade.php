@extends('layouts.buyer')
@section('title', 'Pesanan Saya')

@section('content')

    <div x-data="{
        payModal: false,
        receiveModal: false,
        reviewModal: false,
        activeOrder: null,
        reviewProduct: null,
        actionUrl: '',
        rating: 5,
    
        // Buka Modal Bayar
        openPayModal(order) {
            this.activeOrder = order;
            // Sesuaikan URL dengan route yang didefinisikan
            this.actionUrl = '{{ url('buyer/orders') }}/' + order.order_code + '/pay';
            this.payModal = true;
        },
    
        // Buka Modal Terima
        openReceiveModal(order) {
            this.activeOrder = order;
            this.actionUrl = '{{ url('buyer/orders') }}/' + order.order_code + '/complete';
            this.receiveModal = true;
        },
    
        openReviewModal(order, detail) {
            this.activeOrder = order;
            this.reviewProduct = detail.product;
            this.rating = 0;
            this.reviewModal = true;
        },
    
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        }
    }" class="min-h-screen bg-gray-50/50">

        {{-- ALERT NOTIFICATION --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-24 right-4 z-[999] bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
                <div class="bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg></div>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 animate-fade-up"
                style="animation-delay: 0ms;">
                <div class="flex items-center gap-4">
                    {{-- Back Button --}}
                    <a href="{{ route('buyer.home') }}"
                        class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-green-600 hover:border-green-200 transition shadow-sm group">
                        <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Pesanan Saya</h1>
                        <p class="text-sm text-gray-500">Pantau status pengiriman belanjaanmu.</p>
                    </div>
                </div>
            </div>

            {{-- TABS STATUS --}}
            <div class="sticky top-20 z-30 bg-gray-50/95 backdrop-blur-sm pt-2 pb-4 animate-fade-up"
                style="animation-delay: 100ms;">
                <div class="flex overflow-x-auto no-scrollbar gap-3 pb-2">
                    @php
                        $tabs = [
                            'all' => 'Semua',
                            'pending' => 'Belum Bayar',
                            'paid' => 'Dikemas',
                            'shipped' => 'Dikirim',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp

                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('buyer.orders.index', ['status' => $key]) }}"
                            class="whitespace-nowrap px-5 py-2.5 rounded-full text-sm font-bold transition-all border flex items-center gap-2
                       {{ $status == $key
                           ? 'bg-green-600 text-white border-green-600 shadow-md shadow-green-200'
                           : 'bg-white text-gray-500 border-gray-200 hover:border-green-400 hover:text-green-600' }}">
                            {{ $label }}
                            @if (isset($counts[$key]) && $counts[$key] > 0)
                                <span
                                    class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status == $key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $counts[$key] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- LIST PESANAN --}}
            <div class="space-y-6">
                @forelse($orders as $index => $order)
                    <div class="group bg-white rounded-3xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden animate-fade-up"
                        style="animation-delay: {{ ($index + 2) * 100 }}ms">

                        {{-- Header Card --}}
                        <div
                            class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 text-sm">
                                <div class="flex items-center gap-2 font-bold text-gray-900">
                                    <div class="p-1.5 bg-white rounded-lg border border-gray-200 shadow-sm">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    {{ $order->seller->name ?? 'Toko Tidak Tersedia' }}
                                </div>
                                <span class="text-gray-300">|</span>
                                <span
                                    class="text-gray-500 text-xs">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>

                            {{-- Status Badge --}}
                            <div>
                                @if ($order->status == 'pending')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Belum
                                        Dibayar
                                    </span>
                                @elseif($order->status == 'paid')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Sedang Dikemas
                                    </span>
                                @elseif($order->status == 'shipped')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full border border-purple-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Sedang Dikirim
                                    </span>
                                @elseif($order->status == 'completed')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-100">
                                        Dibatalkan
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Body (Items) --}}
                        <div class="p-6 bg-white relative">
                            {{-- Invoice ID Background Watermark --}}
                            <div
                                class="absolute top-4 right-6 text-6xl font-black text-gray-50 pointer-events-none select-none opacity-50">
                                #{{ substr($order->order_code, -4) }}
                            </div>

                            <div class="space-y-5 relative z-10">
                                @foreach ($order->details as $detail)
                                    <div class="flex gap-4 items-start">
                                        <div
                                            class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 shrink-0">
                                            @if ($detail->product && $detail->product->image)
                                                <img src="{{ asset($detail->product->image) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <img src="https://placehold.co/100?text=X"
                                                    class="w-full h-full object-cover opacity-50 grayscale">
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-gray-900 line-clamp-1">
                                                {{ $detail->product->name ?? 'Produk tidak tersedia' }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $detail->quantity }} x <span class="font-medium">Rp
                                                    {{ number_format($detail->price, 0, ',', '.') }}</span>
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-sm font-bold text-gray-900">Rp
                                                {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Footer (Total & Aksi) --}}
                        <div class="bg-white px-6 py-5 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="text-left w-full sm:w-auto">
                                    <p class="text-xs text-gray-500 mb-0.5 font-medium uppercase tracking-wide">Total
                                        Belanja</p>
                                    <p class="text-xl font-black text-green-600">Rp
                                        {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>

                                <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">

                                    {{-- 1. TOMBOL BAYAR --}}
                                    @if ($order->status == 'pending')
                                        <button @click="openPayModal({{ \Illuminate\Support\Js::from($order) }})"
                                            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition text-sm">
                                            Bayar Sekarang
                                        </button>
                                    @endif

                                    {{-- 2. TOMBOL TERIMA --}}
                                    @if ($order->status == 'shipped')
                                        <button @click="openReceiveModal({{ \Illuminate\Support\Js::from($order) }})"
                                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition text-sm">
                                            Pesanan Diterima
                                        </button>
                                    @endif

                                    {{-- 3. TOMBOL REVIEW (KHUSUS COMPLETED) --}}
                                    @if ($order->status == 'completed')
                                        <div class="flex flex-wrap gap-2 justify-end">
                                            @foreach ($order->details as $detail)
                                                @php
                                                    // PERBAIKAN: Cek review berdasarkan User + Produk saja (Tanpa Order ID)
                                                    $hasReviewed = \App\Models\Review::where('user_id', Auth::id())
                                                        ->where('product_id', $detail->product_id)
                                                        ->exists();
                                                @endphp

                                                @if (!$hasReviewed)
                                                    {{-- Jika belum review, tampilkan tombol --}}
                                                    <button
                                                        @click="openReviewModal({{ \Illuminate\Support\Js::from($order) }}, {{ \Illuminate\Support\Js::from($detail) }})"
                                                        class="px-4 py-2 bg-amber-100 text-amber-700 font-bold text-xs rounded-lg hover:bg-amber-200 transition flex items-center gap-1 border border-amber-200"
                                                        title="Ulas {{ $detail->product->name ?? 'Produk' }}">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                        Ulas {{ Str::limit($detail->product->name ?? 'Item', 10) }}
                                                    </button>
                                                @else
                                                    {{-- Jika sudah review --}}
                                                    <span
                                                        class="px-3 py-2 bg-gray-50 text-gray-400 font-bold text-xs rounded-lg border border-gray-100 cursor-default"
                                                        title="Anda sudah mengulas produk ini">
                                                        {{ Str::limit($detail->product->name ?? 'Item', 8) }} Selesai
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                @empty
                    {{-- EMPTY STATE --}}
                    <div class="py-24 text-center animate-fade-up">
                        <div
                            class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 01-2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">Belum Ada Pesanan</h3>
                        <p class="text-gray-500 mt-2 max-w-md mx-auto">Keranjang belanja masih kosong? Yuk, mulai penuhi
                            kebutuhan dapurmu dengan sayuran segar.</p>
                        <a href="{{ route('buyer.home') }}"
                            class="mt-6 inline-block px-8 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5">Mulai
                            Belanja</a>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="mt-10">
                {{ $orders->links() }}
            </div>
        </div>

        {{-- =========================== --}}
        {{-- MODAL KONFIRMASI PEMBAYARAN --}}
        {{-- =========================== --}}
        <div x-show="payModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;"
            x-cloak>
            {{-- Backdrop --}}
            <div x-show="payModal" @click="payModal = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm">
            </div>

            {{-- Content --}}
            <div x-show="payModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden p-6">

                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Konfirmasi Pembayaran</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Anda akan melakukan pembayaran untuk pesanan <br>
                        <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded"
                            x-text="'#' + activeOrder?.order_code"></span>
                    </p>
                </div>

                <div class="bg-green-50 p-4 rounded-xl border border-green-100 mb-6 text-center">
                    <p class="text-xs text-green-800 uppercase font-bold mb-1">Total Tagihan</p>
                    <p class="text-2xl font-black text-green-700" x-text="formatCurrency(activeOrder?.total_amount)"></p>
                </div>

                <form :action="actionUrl" method="POST" class="flex gap-3">
                    @csrf
                    <button type="button" @click="payModal = false"
                        class="flex-1 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg transition transform hover:-translate-y-0.5">
                        Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>

        {{-- ============================== --}}
        {{-- MODAL KONFIRMASI TERIMA BARANG --}}
        {{-- ============================== --}}
        <div x-show="receiveModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;" x-cloak>
            {{-- Backdrop --}}
            <div x-show="receiveModal" @click="receiveModal = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm">
            </div>

            {{-- Content --}}
            <div x-show="receiveModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden p-6">

                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Pesanan Diterima?</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Pastikan Anda sudah memeriksa kelengkapan dan kondisi barang sebelum melakukan konfirmasi.
                    </p>
                </div>

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            Setelah dikonfirmasi, dana akan diteruskan ke Penjual dan transaksi dianggap selesai. Tindakan
                            ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <form :action="actionUrl" method="POST" class="flex gap-3">
                    @csrf @method('PUT')
                    <button type="button" @click="receiveModal = false"
                        class="flex-1 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">
                        Cek Lagi
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition transform hover:-translate-y-0.5">
                        Ya, Terima Barang
                    </button>
                </form>
            </div>
        </div>

        {{-- MODAL REVIEW --}}
        {{-- MODAL REVIEW --}}
        <div x-show="reviewModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;" x-cloak>
            {{-- Backdrop --}}
            <div x-show="reviewModal" @click="reviewModal = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm">
            </div>

            {{-- Content --}}
            <div x-show="reviewModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden p-6">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div
                        class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-amber-100">
                        <svg class="w-8 h-8 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Beri Ulasan</h3>
                    <p class="text-sm text-gray-500 mt-1">Bagaimana pengalaman Anda dengan produk ini?</p>

                    <div class="mt-4 bg-gray-50 py-2 px-4 rounded-xl border border-gray-100 inline-block max-w-full">
                        <p class="text-sm font-bold text-gray-800 truncate" x-text="reviewProduct?.name"></p>
                    </div>
                </div>

                <form action="{{ route('buyer.reviews.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="order_id" :value="activeOrder?.id">
                    <input type="hidden" name="product_id" :value="reviewProduct?.id">

                    {{-- Bintang Rating --}}
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex justify-center gap-2" x-data="{ hoverRating: 0 }">
                            <template x-for="i in 5">
                                <button type="button" @click="rating = i" @mouseenter="hoverRating = i"
                                    @mouseleave="hoverRating = 0"
                                    class="focus:outline-none transition-transform duration-200 hover:scale-110 p-1">
                                    <svg class="w-10 h-10 transition-colors duration-200"
                                        :class="(hoverRating >= i || rating >= i) ? 'text-amber-400 drop-shadow-sm' :
                                        'text-gray-200'"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" x-model="rating">

                        {{-- Label Rating Helper --}}
                        <p class="text-xs font-bold uppercase tracking-wide transition-all duration-300"
                            :class="rating > 0 ? 'text-amber-500' : 'text-gray-300'"
                            x-text="['Pilih Rating', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'][rating]">
                        </p>
                    </div>

                    {{-- Komentar --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Ulasan Anda</label>
                        <textarea name="comment" rows="3" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition placeholder-gray-400 resize-none"
                            placeholder="Ceritakan detail kualitas produk, pengiriman, dll..."></textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="reviewModal = false"
                            class="flex-1 py-3.5 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                            Batal
                        </button>
                        <button type="submit" :disabled="rating === 0"
                            class="flex-1 py-3.5 text-white font-bold rounded-xl shadow-lg transition transform active:scale-95 text-sm flex justify-center items-center gap-2"
                            :class="rating === 0 ? 'bg-gray-300 cursor-not-allowed' :
                                'bg-green-600 hover:bg-green-700 shadow-green-200'">
                            <span>Kirim Ulasan</span>
                            <svg x-show="rating > 0" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CUSTOM STYLES --}}
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

@endsection
