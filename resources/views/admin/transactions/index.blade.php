@extends('layouts.admin')
@section('title', 'Monitoring Transaksi')

@section('content')

    <div x-data="{
        detailModal: false,
        modalOrder: null,
        searchQuery: '{{ request('search', '') }}',
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.statusSelect, { create: false, placeholder: 'Semua Status', onChange: () => $refs.filterForm.submit() });
        new TomSelect($refs.sellerSelect, { create: false, placeholder: 'Semua Seller', onChange: () => $refs.filterForm.submit() });
        new TomSelect($refs.perPageSelect, { create: false, controlInput: null, onChange: () => $refs.filterForm.submit() });
    });">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800">Monitoring Transaksi</h1>
                <p class="text-gray-500 mt-1">Pantau semua pesanan yang masuk (Split Order per Seller).</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.transactions.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Transaksi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input type="text" name="search" placeholder="No Invoice atau Nama Buyer..."
                                x-model="searchQuery" @keydown.enter.prevent="$refs.filterForm.submit()"
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none transition-all">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                <button :type="searchQuery ? 'button' : 'submit'"
                                    @click="if (searchQuery) { searchQuery = ''; $nextTick(() => $refs.filterForm.submit()); }"
                                    class="p-1 text-gray-400 rounded-full hover:text-red-600 hover:bg-red-100 transition"
                                    :title="searchQuery ? 'Bersihkan' : 'Cari'">
                                    <svg x-show="searchQuery" style="display: none;" class="w-5 h-5"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                    <svg x-show="!searchQuery" style="display: none;" class="w-5 h-5"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seller</label>
                        <select name="seller_id" x-ref="sellerSelect">
                            <option value="">Semua Seller</option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->id }}" @selected(request('seller_id') == $seller->id)>{{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" x-ref="statusSelect">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') == 'pending')>Menunggu Bayar</option>
                            <option value="paid" @selected(request('status') == 'paid')>Dibayar (Proses)</option>
                            <option value="shipped" @selected(request('status') == 'shipped')>Dikirim</option>
                            <option value="completed" @selected(request('status') == 'completed')>Selesai</option>
                            <option value="cancelled" @selected(request('status') == 'cancelled')>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tampil</label>
                        <select name="per_page" x-ref="perPageSelect">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>{{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <a href="{{ route('admin.transactions.index') }}"
                            class="w-full h-10 px-5 py-5.5 flex items-center justify-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Seller</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm font-medium text-green-600 font-mono">
                                    #{{ $order->order_code }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y, H:i') }}
                                    WIB
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ $order->user->name ?? 'Guest' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 font-medium">
                                    {{ $order->seller->name ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-900">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status == 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status == 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <button
                                        @click="
                                    detailModal = true; 
                                    modalOrder = {{ $order->toJson() }}; 
                                    modalOrder.user = {{ $order->user->toJson() }}; 
                                    modalOrder.seller = {{ $order->seller->toJson() }}; 
                                    modalOrder.details = {{ $order->details->load('product')->toJson() }};
                                "
                                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition"
                                        title="Lihat Detail">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Tinjau
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 rounded-full p-4 mb-4">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Belum Ada Transaksi</h3>
                                        <p class="text-sm text-gray-500 max-w-xs mt-1">Belum ada data pesanan yang masuk
                                            sesuai filter Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>

        <div x-show="detailModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div x-show="detailModal" class="relative w-full max-w-3xl bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-green-600 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white">Detail Pesanan</h3>
                        <p class="text-sm text-green-100 font-mono" x-text="'#' + modalOrder?.order_code"></p>
                    </div>
                    <button @click="detailModal = false" class="text-green-100 hover:text-white transition"><svg
                            class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Pembeli</p>
                            <p class="font-medium text-gray-900" x-text="modalOrder?.user?.name"></p>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="modalOrder?.address"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Penjual</p>
                            <p class="font-medium text-gray-900" x-text="modalOrder?.seller?.name"></p>
                            <p class="text-xs text-gray-500 mt-0.5"
                                x-text="modalOrder?.seller?.address || 'Alamat toko tidak tersedia'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Status</p>
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-bold uppercase"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': modalOrder?.status == 'pending',
                                    'bg-blue-100 text-blue-800': modalOrder?.status == 'paid',
                                    'bg-purple-100 text-purple-800': modalOrder?.status == 'shipped',
                                    'bg-green-100 text-green-800': modalOrder?.status == 'completed',
                                    'bg-red-100 text-red-800': modalOrder?.status == 'cancelled'
                                }"
                                x-text="modalOrder?.status">
                            </span>
                            <p class="text-xs text-gray-500 mt-2">Method: <span class="uppercase font-semibold"
                                    x-text="modalOrder?.payment_method"></span></p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Rincian Produk</h4>
                        <div class="space-y-3">
                            <template x-for="item in modalOrder?.details" :key="item.id">
                                <div class="flex justify-between items-center border-b border-gray-100 pb-2 last:border-0">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded mr-3 overflow-hidden border">
                                            <img src="https://placehold.co/100x100/e0e0e0/757575?text=IMG"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"
                                                x-text="item.product?.name || 'Produk dihapus'"></p>
                                            <p class="text-xs text-gray-500"
                                                x-text="item.quantity + ' x ' + formatCurrency(item.price)"></p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(item.subtotal)"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <span class="text-base font-bold text-gray-700">Total Pesanan</span>
                        <span class="text-xl font-bold text-green-600"
                            x-text="formatCurrency(modalOrder?.total_amount)"></span>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 text-right border-t border-gray-200">
                    <button @click="detailModal = false"
                        class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium shadow-sm">Tutup</button>
                </div>
            </div>
        </div>

    </div>
@endsection
