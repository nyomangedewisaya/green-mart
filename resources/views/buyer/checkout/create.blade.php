@extends('layouts.buyer')
@section('title', 'Konfirmasi Pesanan')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 font-inter"
     x-data="checkoutSystem()"
     x-init="
        @foreach($groupedCarts as $sellerId => $items)
            shippingSelection[{{ $sellerId }}] = ''; 
        @endforeach
        initCheckout();
     ">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- HEADER: Back Button & Title --}}
        <div class="flex items-center gap-4 mb-8 animate-fade-in">
            <a href="{{ route('buyer.cart.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-green-600 hover:border-green-200 transition shadow-sm group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Konfirmasi Pesanan</h1>
                <p class="text-sm text-gray-500">Periksa kembali rincian pesanan sebelum membayar.</p>
            </div>
        </div>
        
        <form action="{{ route('buyer.checkout.store') }}" method="POST" id="checkoutForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf
            
            @foreach($cartIds as $id)
                <input type="hidden" name="cart_ids[]" value="{{ $id }}">
            @endforeach

            {{-- KOLOM KIRI --}}
            <div class="lg:col-span-8 space-y-6 animate-fade-up" style="animation-delay: 100ms;">
                
                {{-- ALAMAT --}}
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-200 hover:shadow-md transition duration-300">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="bg-green-50 p-2 rounded-lg text-green-600 border border-green-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        Alamat Pengiriman
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Penerima</label>
                            <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500 font-medium cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" required value="{{ old('phone', Auth::user()->phone) }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition font-medium placeholder-gray-300" placeholder="08xx">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="3" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition placeholder-gray-300" placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan...">{{ old('address', Auth::user()->address) }}</textarea>
                    </div>
                    <div class="mt-5 flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <input type="checkbox" name="save_address" id="save_address" value="1" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                        <label for="save_address" class="ml-3 text-sm font-medium text-gray-600 cursor-pointer select-none">Simpan alamat ini untuk pesanan berikutnya</label>
                    </div>
                </div>

                {{-- ITEM & KURIR --}}
                <div class="space-y-6">
                    @foreach($groupedCarts as $sellerId => $items)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-300 animate-fade-up" style="animation-delay: {{ ($loop->iteration * 100) + 100 }}ms">
                            <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center gap-3">
                                <div class="p-1.5 bg-white rounded-lg border border-gray-200">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="font-bold text-gray-900">{{ $items->first()->product->seller->name }}</span>
                            </div>

                            <div class="p-6">
                                {{-- List Produk --}}
                                <div class="space-y-5 mb-6">
                                    @foreach($items as $item)
                                        @php 
                                            $price = $item->product->price - ($item->product->price * $item->product->discount / 100);
                                            $subtotal = $price * $item->quantity;
                                        @endphp
                                        <div class="flex gap-5 items-start group/item">
                                            <div class="w-16 h-16 rounded-xl bg-gray-100 shrink-0 overflow-hidden border border-gray-100">
                                                <img src="{{ $item->product->image ? asset($item->product->image) : 'https://placehold.co/100' }}" class="w-full h-full object-cover group-hover/item:scale-105 transition duration-500">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 line-clamp-2 mb-1">{{ $item->product->name }}</p>
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded text-[10px] font-bold">{{ $item->quantity }}x</span>
                                                    <span>Rp {{ number_format($price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- PILIHAN KURIR --}}
                                <div class="bg-green-50/50 p-5 rounded-2xl border border-green-100 hover:border-green-200 transition">
                                    <label class="block text-xs font-bold text-green-800 uppercase mb-2 tracking-wide">Pilih Pengiriman <span class="text-red-500">*</span></label>
                                    
                                    <div class="relative">
                                        <select name="shipping[{{ $sellerId }}]" 
                                                x-model="shippingSelection[{{ $sellerId }}]" 
                                                @change="calculateGrandTotal()"
                                                class="appearance-none w-full pl-4 pr-10 py-3 bg-white border border-green-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none cursor-pointer transition shadow-sm text-gray-700 hover:border-green-300">
                                            
                                            <option value="" disabled selected>-- Pilih Kurir --</option>
                                            
                                            @foreach($couriers as $courier)
                                                <option value="{{ $courier->name }} {{ $courier->service }}|{{ $courier->cost }}">
                                                    {{ $courier->name }} {{ $courier->service }} - Rp {{ number_format($courier->cost, 0, ',', '.') }} ({{ $courier->estimation }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-green-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- KOLOM KANAN (STICKY SUMMARY) --}}
            <div class="lg:col-span-4 animate-fade-up" style="animation-delay: 200ms;">
                <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-200 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Ringkasan Pembayaran</h3>

                    <div class="space-y-4 text-sm text-gray-600 mb-6 border-b border-gray-100 pb-6">
                        <div class="flex justify-between items-center">
                            <span>Total Harga Barang</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($subtotalProduct, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Total Ongkos Kirim</span>
                            <span class="font-bold text-green-600" x-text="formatCurrency(totalShippingCost)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Biaya Layanan</span>
                            <span class="font-medium text-gray-900">Rp 1.000</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-8 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="font-bold text-gray-900 text-sm uppercase tracking-wide">Total Tagihan</span>
                        <span class="text-2xl font-black text-green-600 tracking-tight" x-text="formatCurrency(grandTotal)"></span>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Metode Pembayaran</label>
                        <div class="relative">
                            <select name="payment_method" class="appearance-none w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 hover:border-green-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-green-500 focus:bg-white outline-none cursor-pointer transition">
                                <option value="COD">🏠 COD (Bayar di Tempat)</option>
                                <option value="Transfer Bank">🏦 Transfer Bank</option>
                                <option value="E-Wallet">📱 E-Wallet (Gopay/OVO)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="!isShippingValid"
                            class="w-full py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg shadow-green-200/50 disabled:shadow-none transition transform hover:-translate-y-0.5 flex justify-center items-center gap-2 text-sm tracking-wide">
                        <span x-show="isShippingValid">Buat Pesanan</span>
                        <span x-show="!isShippingValid">Pilih Kurir Dulu</span>
                        <svg x-show="isShippingValid" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>

                    <p class="text-[10px] text-center text-gray-400 mt-4 leading-relaxed">
                        Dengan melanjutkan, Anda menyetujui <a href="#" class="underline hover:text-green-600">Syarat & Ketentuan</a> Green Mart.
                    </p>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function checkoutSystem() {
        return {
            productSubtotal: {{ $subtotalProduct }},
            serviceFee: 1000,
            shippingSelection: {}, 
            totalShippingCost: 0,
            grandTotal: {{ $subtotalProduct + 1000 }},
            
            initCheckout() {
                // Logic sudah di x-init HTML
            },

            get isShippingValid() {
                const totalSellers = {{ $groupedCarts->count() }};
                let selectedCount = 0;
                for (let key in this.shippingSelection) {
                    if (this.shippingSelection[key] !== "") {
                        selectedCount++;
                    }
                }
                return totalSellers === selectedCount;
            },

            calculateGrandTotal() {
                let shippingTotal = 0;
                for (const [sellerId, value] of Object.entries(this.shippingSelection)) {
                    if (value) {
                        const cost = parseInt(value.split('|')[1]);
                        shippingTotal += cost;
                    }
                }
                this.totalShippingCost = shippingTotal;
                this.grandTotal = this.productSubtotal + this.totalShippingCost + this.serviceFee;
            },

            formatCurrency(val) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
            }
        }
    }
</script>

<style>
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
</style>
@endsection