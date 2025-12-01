@extends('layouts.buyer')
@section('title', 'Keranjang Belanja')

@section('content')

{{-- 1. PERSIAPAN DATA PHP --}}
@php
    $cartData = [];
    
    foreach($carts as $sellerName => $items) {
        $productsList = [];
        
        foreach($items as $item) {
            if (!$item->product) continue; 

            $price = $item->product->price;
            $discount = $item->product->discount ?? 0;
            $finalPrice = $price - ($price * ($discount / 100));
            
            $productsList[] = [
                'uuid'       => $item->uuid,
                'id'         => $item->id,
                'name'       => $item->product->name,
                'image'      => $item->product->image ? asset($item->product->image) : 'https://placehold.co/100?text=Produk',
                'category'   => $item->product->category->name ?? 'Umum',
                'price'      => $price,
                'discount'   => $discount,
                'stock'      => $item->product->stock,
                'quantity'   => $item->quantity,
                'final_price'=> $finalPrice,
                'checked'    => false,
                'loading'    => false
            ];
        }

        if (!empty($productsList)) {
            $cartData[] = [
                'seller' => $sellerName,
                'items'  => $productsList
            ];
        }
    }
@endphp

{{-- 2. TAMPILAN UTAMA --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen font-inter"
     x-data="cartSystem()"
     x-init="initCart()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8 animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-green-50 text-green-600 rounded-xl border border-green-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Keranjang Belanja</h1>
        </div>

        {{-- Tombol Kembali --}}
        <template x-if="cartGroups.length > 0">
            <a href="{{ route('buyer.home') }}" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-green-600 hover:bg-white px-4 py-2 rounded-xl transition border border-transparent hover:border-gray-200 hover:shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Lanjut Belanja
            </a>
        </template>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    <div x-show="notification.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-24 right-4 z-[100] px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm font-bold border border-white/10 backdrop-blur-md"
         :class="notification.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
         style="display: none;" x-cloak>
         <span x-text="notification.message"></span>
    </div>

    {{-- KONTEN CART --}}
    <template x-if="cartGroups.length > 0">
        <div>
            {{-- FORM WRAPPER --}}
            <form action="{{ route('buyer.checkout.create') }}" method="POST" id="checkoutForm">
                @csrf

                {{-- LIST PRODUK --}}
                <div class="space-y-6 mb-32"> {{-- Margin bottom besar agar tidak tertutup footer --}}
                    <template x-for="(group, gIndex) in cartGroups" :key="gIndex">
                        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition duration-300 cart-item-anim"
                             :style="`animation-delay: ${gIndex * 100}ms`">
                            
                            {{-- Header Toko --}}
                            <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center gap-2.5">
                                <div class="p-1.5 bg-white rounded-lg border border-gray-200">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="font-bold text-gray-900 text-sm tracking-wide" x-text="group.seller"></span>
                            </div>
    
                            {{-- Items --}}
                            <div class="divide-y divide-gray-100">
                                <template x-for="(item, iIndex) in group.items" :key="item.uuid">
                                    <div class="p-5 sm:p-6 flex items-start gap-5 transition-all duration-200 group hover:bg-gray-50/30" 
                                         :class="{'opacity-50 pointer-events-none bg-gray-50': item.loading}">
                                        
                                        {{-- Checkbox --}}
                                        <div class="pt-8 shrink-0">
                                            <input type="checkbox" 
                                                   :value="item.id" 
                                                   x-model="item.checked"
                                                   @change="recalculate()"
                                                   class="w-5 h-5 text-green-600 border-gray-300 rounded-md focus:ring-green-500 cursor-pointer transition shadow-sm">
                                        </div>
    
                                        {{-- Gambar --}}
                                        <div class="w-24 h-24 bg-gray-100 rounded-xl overflow-hidden shrink-0 border border-gray-200 relative">
                                            <img :src="item.image" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                            <template x-if="item.stock <= 5">
                                                <div class="absolute bottom-0 left-0 right-0 bg-red-500/90 text-white text-[9px] text-center font-bold py-0.5 backdrop-blur-sm">Sisa <span x-text="item.stock"></span></div>
                                            </template>
                                        </div>
    
                                        {{-- Detail Produk --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1" x-text="item.category"></p>
                                            <a href="#" class="text-base font-bold text-gray-900 line-clamp-2 leading-snug hover:text-green-600 transition mb-1" x-text="item.name"></a>
                                            
                                            <div class="flex flex-col sm:flex-row sm:items-end justify-between mt-3 gap-4">
                                                <div>
                                                    <template x-if="item.discount > 0">
                                                        <div class="flex items-center gap-2 mb-0.5">
                                                            <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-md border border-red-100" x-text="'-' + item.discount + '%'"></span>
                                                            <span class="text-xs text-gray-400 line-through decoration-gray-300" x-text="formatCurrency(item.price)"></span>
                                                        </div>
                                                    </template>
                                                    <p class="text-lg font-black text-green-600" x-text="formatCurrency(item.final_price)"></p>
                                                </div>
    
                                                {{-- Qty Control (PERBAIKAN POSISI TENGAH) --}}
                                                <div class="flex items-center justify-center border border-gray-200 rounded-xl h-10 w-32 bg-white shadow-sm hover:border-green-300 transition">
                                                    
                                                    <button type="button" @click="updateQty(gIndex, iIndex, -1)" 
                                                        class="w-10 h-full flex items-center justify-center text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-l-xl transition text-xl font-bold leading-none pb-1">
                                                        -
                                                    </button>
                                                    
                                                    <input type="text" x-model="item.quantity" readonly 
                                                        class="w-12 h-full text-center text-sm font-bold text-gray-900 border-x border-gray-100 p-0 focus:ring-0 bg-transparent leading-none">
                                                    
                                                    <button type="button" @click="updateQty(gIndex, iIndex, 1)" 
                                                        class="w-10 h-full flex items-center justify-center text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-r-xl transition text-xl font-bold leading-none pb-1">
                                                        +
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
    
                                        {{-- Hapus Button --}}
                                        <button type="button" @click="deleteItem(gIndex, iIndex)" class="text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-xl transition self-start mt-6" title="Hapus Item">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- INPUT HIDDEN ID --}}
                <template x-for="group in cartGroups">
                    <template x-for="item in group.items">
                        <template x-if="item.checked">
                            <input type="hidden" name="cart_ids[]" :value="item.id">
                        </template>
                    </template>
                </template>

            </form>
        </div>
    </template>

    {{-- EMPTY STATE --}}
    <template x-if="cartGroups.length === 0">
        <div class="flex flex-col items-center justify-center py-24 animate-fade-in">
            <div class="bg-green-50 p-8 rounded-full mb-6 border border-green-100 shadow-sm animate-bounce-slow">
                <svg class="w-20 h-20 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 mb-2">Keranjang Kosong</h2>
            <p class="text-gray-500 max-w-xs text-center leading-relaxed mb-8">Wah, keranjang belanjaanmu masih kosong nih. Yuk mulai penuhi kebutuhan dapurmu!</p>
            <a href="{{ route('buyer.home') }}" class="px-8 py-3.5 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition transform hover:-translate-y-1 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Mulai Belanja Sekarang
            </a>
        </div>
    </template>

    {{-- FIXED BOTTOM BAR (TOMBOL CHECKOUT DISINI) --}}
    {{-- PENTING: Ini sekarang di luar template cartGroups agar selalu dirender --}}
    <div x-show="cartGroups.length > 0" class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-xl border-t border-gray-200 p-4 shadow-[0_-5px_30px_rgba(0,0,0,0.08)] z-50 animate-slide-up" x-cloak>
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            
            {{-- Pilih Semua --}}
            <div class="flex items-center gap-3 w-full sm:w-auto cursor-pointer p-2 hover:bg-gray-50 rounded-lg transition" @click="toggleSelectAll()">
                <div class="w-5 h-5 border-2 rounded flex items-center justify-center transition-all duration-200"
                     :class="selectAll ? 'bg-green-600 border-green-600 scale-110' : 'border-gray-300 bg-white'">
                     <svg x-show="selectAll" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-700 select-none">Pilih Semua (<span x-text="totalItems"></span>)</span>
            </div>
            
            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end bg-gray-50 sm:bg-transparent p-3 sm:p-0 rounded-xl border sm:border-none border-gray-100">
                <div class="text-right">
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Total Pembayaran</p>
                    <p class="text-xl font-black text-green-600 tracking-tight" x-text="formatCurrency(grandTotal)"></p>
                </div>
                
                {{-- TOMBOL CHECKOUT --}}
                <button type="button" 
                    @click="openCheckoutModal()" 
                    :disabled="totalSelected === 0"
                    class="px-8 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg shadow-green-200/50 disabled:shadow-none transition transform active:scale-95 flex items-center gap-2">
                    Checkout (<span x-text="totalSelected"></span>)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI CHECKOUT --}}
    <div x-show="checkoutModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;" x-cloak>
        {{-- Backdrop --}}
        <div x-show="checkoutModal" @click="checkoutModal = false" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm">
        </div>

        {{-- Content --}}
        <div x-show="checkoutModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-4"
             class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">
            
            <div class="bg-green-600 px-6 py-5 flex justify-between items-center text-white shrink-0">
                <div>
                    <h3 class="text-xl font-bold">Konfirmasi Checkout</h3>
                    <p class="text-green-100 text-xs mt-0.5">Pastikan pesanan Anda sudah benar.</p>
                </div>
                <button @click="checkoutModal = false" class="hover:bg-green-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <div class="p-6 overflow-y-auto custom-scroll">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Rincian Pesanan:</p>
                
                <div class="space-y-4">
                    <template x-for="group in cartGroups">
                        <div x-show="group.items.some(i => i.checked)" class="border border-gray-200 rounded-2xl overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200 font-bold text-xs text-gray-700 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span x-text="group.seller"></span>
                            </div>
                            <div class="p-4 space-y-4">
                                <template x-for="item in group.items">
                                    <template x-if="item.checked">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-start gap-3">
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 shrink-0 overflow-hidden border border-gray-200">
                                                    <img :src="item.image" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-900 line-clamp-1" x-text="item.name"></p>
                                                    <p class="text-[10px] text-gray-500 mt-0.5"><span x-text="item.quantity"></span> x <span x-text="formatCurrency(item.final_price)"></span></p>
                                                </div>
                                            </div>
                                            <p class="text-sm font-bold text-gray-800" x-text="formatCurrency(item.final_price * item.quantity)"></p>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="p-6 bg-gray-50 border-t border-gray-200 shrink-0">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-gray-500 font-bold text-sm uppercase">Total Estimasi</span>
                    <span class="text-2xl font-black text-green-600" x-text="formatCurrency(grandTotal)"></span>
                </div>
                
                <button @click="document.getElementById('checkoutForm').submit()" class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                    Lanjut Pembayaran <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function cartSystem() {
        return {
            cartGroups: @json($cartData),
            selectAll: false,
            checkoutModal: false,
            notification: { show: false, type: 'success', message: '' },

            initCart() {
                this.recalculate();
            },

            get totalItems() {
                return this.cartGroups.reduce((sum, group) => sum + group.items.length, 0);
            },
            get totalSelected() {
                let count = 0;
                this.cartGroups.forEach(g => g.items.forEach(i => { if(i.checked) count++ }));
                return count;
            },
            get grandTotal() {
                let total = 0;
                this.cartGroups.forEach(g => {
                    g.items.forEach(i => {
                        if (i.checked) total += (i.final_price * i.quantity);
                    });
                });
                return total;
            },

            recalculate() {
                let allChecked = true;
                let hasItems = false;
                this.cartGroups.forEach(g => {
                    g.items.forEach(i => {
                        hasItems = true;
                        if (!i.checked) allChecked = false;
                    });
                });
                if (!hasItems) allChecked = false;
                this.selectAll = allChecked;
            },

            toggleSelectAll() {
                this.selectAll = !this.selectAll;
                this.cartGroups.forEach(g => {
                    g.items.forEach(i => { i.checked = this.selectAll; });
                });
            },

            openCheckoutModal() { this.checkoutModal = true; },

            showNotification(type, msg) {
                this.notification = { show: true, type: type, message: msg };
                setTimeout(() => { this.notification.show = false }, 3000);
            },

            formatCurrency(val) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
            },

            updateQty(gIndex, iIndex, change) {
                let item = this.cartGroups[gIndex].items[iIndex];
                let newQty = item.quantity + change;

                if (newQty < 1) return;
                if (newQty > item.stock) {
                    this.showNotification('error', 'Stok maksimum tercapai');
                    return;
                }

                item.quantity = newQty;
                
                fetch(`{{ url('buyer/cart') }}/${item.uuid}`, {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQty })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        item.quantity -= change; 
                        this.showNotification('error', data.message || 'Gagal update stok');
                    }
                })
                .catch(err => {
                    item.quantity -= change;
                    this.showNotification('error', 'Terjadi kesalahan jaringan');
                });
            },

            deleteItem(gIndex, iIndex) {
                let item = this.cartGroups[gIndex].items[iIndex];
                item.loading = true;

                fetch(`{{ url('buyer/cart') }}/${item.uuid}`, {
                    method: 'DELETE',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => {
                    if(res.ok) {
                        this.cartGroups[gIndex].items.splice(iIndex, 1);
                        if(this.cartGroups[gIndex].items.length === 0) {
                            this.cartGroups.splice(gIndex, 1);
                        }
                        this.showNotification('success', 'Produk dihapus');
                        this.recalculate();
                    } else {
                        item.loading = false;
                        this.showNotification('error', 'Gagal menghapus produk');
                    }
                });
            }
        }
    }
</script>

<style>
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .cart-item-anim { animation: fadeUp 0.5s ease-out forwards; opacity: 0; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }

    @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
    .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    @keyframes bounceSlow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .animate-bounce-slow { animation: bounceSlow 3s infinite ease-in-out; }
</style>
@endsection