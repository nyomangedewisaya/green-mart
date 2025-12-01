@extends('layouts.buyer')
@section('title', $seller->name)

@section('content')

    {{-- WRAPPER UTAMA --}}
    <div x-data="{
        productModal: false,
        reportModal: false,
        activeProduct: null,
        qty: 1,
        activeTab: 'products',
        modalTab: 'desc',
        reportTarget: 'product',
    
        openModal(product) {
            this.activeProduct = product;
            this.qty = 1;
            this.modalTab = 'desc';
            this.productModal = true;
        },
    
        openProductReport() {
            this.reportTarget = 'product';
            this.reportModal = true;
        },
    
        openSellerReport() {
            this.reportTarget = 'seller';
            this.reportModal = true;
        },
    
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        getImageUrl(path) {
            if (!path) return 'https://placehold.co/500?text=No+Image';
            if (path.startsWith('http')) return path;
            return '{{ asset('') }}' + path;
        }
    }">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="fixed top-24 right-4 z-[999] bg-green-600 text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
                <div class="bg-white/20 p-1 rounded-full shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-medium text-xs">{{ session('success') }}</span>
            </div>
        @endif

        {{-- HERO BANNER --}}
        <div class="relative bg-white mb-10">
            {{-- Back Button --}}
            <a href="{{ route('buyer.home') }}"
                class="absolute top-6 left-4 md:left-8 z-20 bg-white/90 backdrop-blur hover:bg-white text-gray-700 p-2 rounded-full shadow-sm border border-gray-100 transition transform hover:scale-105 group">
                <svg class="w-5 h-5 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>

            {{-- Banner --}}
            <div class="h-40 md:h-56 w-full bg-gray-100 overflow-hidden relative">
                <img src="{{ $seller->banner ? asset($seller->banner) : 'https://placehold.co/1200x400/10b981/ffffff?text=Green+Mart+Store' }}"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            </div>

            {{-- Profile Info Card --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative -mt-12 z-10">
                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 flex flex-col md:flex-row items-start md:items-center gap-5 animate-fade-up">

                    {{-- Logo --}}
                    <div class="relative shrink-0 -mt-10 md:mt-0">
                        <img src="{{ $seller->logo ? asset($seller->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($seller->name) . '&background=10b981&color=fff' }}"
                            class="w-20 h-20 md:w-24 md:h-24 rounded-2xl border-4 border-white shadow-md object-cover bg-white">
                        @if ($seller->is_verified)
                            <div class="absolute -bottom-1 -right-1 bg-blue-500 text-white p-0.5 rounded-full border-2 border-white shadow-sm"
                                title="Terverifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info Text --}}
                    <div class="flex-1 w-full">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                            <div>
                                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">{{ $seller->name }}
                                </h1>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $seller->address ?? 'Lokasi tidak tersedia' }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <button
                                    class="px-4 py-2 bg-green-50 text-green-700 text-xs font-bold rounded-lg border border-green-100 hover:bg-green-100 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Chat Toko
                                </button>
                                <button
                                    class="p-2 bg-gray-50 text-gray-500 rounded-lg border border-gray-200 hover:bg-gray-100 transition"
                                    title="Bagikan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </button>
                                <button @click="openSellerReport()"
                                    class="p-2 bg-red-50 text-red-500 rounded-lg border border-red-100 hover:bg-red-100 transition"
                                    title="Laporkan Toko">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13a1 1 0 011-1h1.586l-.3 3h-.286a1 1 0 01-1-1z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Stats Row --}}
                        <div class="flex items-center gap-6 mt-5 pt-5 border-t border-gray-50 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <p class="font-bold text-gray-900">{{ number_format($sellerRating, 1) }} <span
                                        class="font-normal text-gray-400">Rating</span></p>
                            </div>
                            <div class="w-px h-4 bg-gray-200"></div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-gray-900">{{ $products->total() }} <span
                                        class="font-normal text-gray-400">Produk</span></p>
                            </div>
                            <div class="w-px h-4 bg-gray-200"></div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-gray-900">{{ $totalSold }} <span
                                        class="font-normal text-gray-400">Terjual</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT AREA --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

            {{-- Tabs Header & Search --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-100 mb-8">

                {{-- Tab Buttons --}}
                <div class="flex gap-8">
                    <button @click="activeTab = 'products'" class="pb-3 text-sm font-bold transition border-b-2 -mb-px"
                        :class="activeTab === 'products' ? 'text-green-600 border-green-600' :
                            'text-gray-400 border-transparent hover:text-gray-800'">
                        Produk Toko
                    </button>
                    <button @click="activeTab = 'about'" class="pb-3 text-sm font-bold transition border-b-2 -mb-px"
                        :class="activeTab === 'about' ? 'text-green-600 border-green-600' :
                            'text-gray-400 border-transparent hover:text-gray-800'">
                        Tentang Toko
                    </button>
                </div>

                {{-- Search Input --}}
                <div x-show="activeTab === 'products'" class="w-full md:max-w-xs pb-2 md:pb-0">
                    <form action="{{ route('buyer.sellers.show', $seller->slug) }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari di toko ini..."
                            class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-green-500 focus:bg-white outline-none transition placeholder-gray-400">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-2.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </form>
                </div>
            </div>

            {{-- TAB: PRODUCTS --}}
            <div x-show="activeTab === 'products'" class="animate-fade-up">
                @if (request('search'))
                    <div
                        class="mb-6 flex items-center gap-2 text-xs text-gray-500 bg-gray-50 w-fit px-3 py-1.5 rounded-lg border border-gray-100">
                        <p>Pencarian: <strong class="text-gray-800">"{{ request('search') }}"</strong></p>
                        <a href="{{ route('buyer.sellers.show', $seller->slug) }}"
                            class="text-red-500 hover:underline font-bold ml-2">&times; Hapus</a>
                    </div>
                @endif

                @if ($products->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($products as $index => $product)
                            {{-- CARD PRODUK (LENGKAP DENGAN IS_FEATURED & CART) --}}
                            <div class="group bg-white rounded-xl shadow-sm transition-all duration-300 flex flex-col overflow-hidden relative animate-fade-up
                                    {{ $product->is_featured ? 'ring-2 ring-amber-400 border-transparent shadow-lg shadow-amber-100 transform -translate-y-1' : 'border border-gray-200 hover:shadow-xl hover:-translate-y-1 hover:border-green-300' }}"
                                style="animation-delay: {{ $index * 50 }}ms">

                                {{-- BADGES CONTAINER --}}
                                <div class="absolute top-2 left-2 z-20 flex flex-col gap-1.5 items-start">
                                    @if ($product->is_featured)
                                        <div
                                            class="bg-amber-400 text-white text-[9px] font-bold px-2 py-1 rounded-md shadow-md flex items-center gap-1">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Pilihan
                                        </div>
                                    @endif
                                    @if ($product->discount > 0)
                                        <div
                                            class="bg-red-500 text-white text-[9px] font-bold px-2 py-1 rounded-md shadow-md">
                                            Hemat {{ $product->discount }}%</div>
                                    @endif
                                </div>

                                {{-- WISHLIST BUTTON --}}
                                <div class="absolute top-2 right-2 z-20">
                                    <form action="{{ route('buyer.wishlist.toggle') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit"
                                            class="flex items-center gap-1 bg-white/90 backdrop-blur-md p-1.5 rounded-full shadow-sm border border-gray-100 hover:scale-110 active:scale-95 transition group/heart"
                                            title="Wishlist">
                                            <svg class="w-4 h-4 {{ $product->is_wishlisted ? 'text-red-500 fill-current' : 'text-gray-400 fill-transparent group-hover/heart:text-red-400' }} transition-colors"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            @if ($product->wishlists_count > 0)
                                                <span
                                                    class="text-[10px] font-bold text-gray-600 pr-1">{{ $product->wishlists_count }}</span>
                                            @endif
                                        </button>
                                    </form>
                                </div>

                                {{-- IMAGE --}}
                                <div class="relative aspect-square bg-gray-50 overflow-hidden cursor-pointer"
                                    @click="openModal({{ \Illuminate\Support\Js::from($product->load('category', 'reviews.user', 'seller')) }})">
                                    <img src="{{ $product->image ? asset($product->image) : 'https://placehold.co/300?text=Produk' }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500 mix-blend-multiply">

                                    <div
                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-end justify-center p-3 opacity-0 group-hover:opacity-100">
                                        <button
                                            class="w-full py-2 bg-white/90 backdrop-blur text-green-700 font-bold text-xs rounded-xl shadow-lg">Lihat
                                            Detail</button>
                                    </div>
                                </div>

                                {{-- CONTENT --}}
                                <div class="p-3 flex flex-col flex-1">
                                    <span
                                        class="text-[9px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">{{ $product->category->name }}</span>
                                    <h3 class="text-xs font-bold text-gray-900 leading-snug line-clamp-2 mb-2 h-8 group-hover:text-green-600 transition-colors cursor-pointer"
                                        @click="openModal({{ \Illuminate\Support\Js::from($product->load('category', 'reviews.user', 'seller')) }})">
                                        {{ $product->name }}
                                    </h3>

                                    <div class="mt-auto flex items-end justify-between pt-2 border-t border-gray-50/50">
                                        <div>
                                            @if ($product->discount > 0)
                                                <p class="text-[9px] text-gray-400 line-through">Rp
                                                    {{ number_format($product->price, 0, ',', '.') }}</p>
                                            @endif
                                            @php $final = $product->price - ($product->price * ($product->discount / 100)); @endphp
                                            <p class="text-sm font-black text-green-600">Rp
                                                {{ number_format($final, 0, ',', '.') }}</p>
                                        </div>

                                        {{-- Add to Cart Button (FORM) --}}
                                        <form action="{{ route('buyer.cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit"
                                                class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-green-600 hover:text-white transition shadow-sm group/cart">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8 text-xs">{{ $products->links() }}</div>
                @else
                    <div class="py-16 text-center animate-fade-up">
                        <div
                            class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Produk Tidak Ditemukan</h3>
                        <p class="text-xs text-gray-500 mt-1">Kata kunci pencarian tidak cocok.</p>
                    </div>
                @endif
            </div>

            {{-- TAB: ABOUT --}}
            <div x-show="activeTab === 'about'" style="display: none;" class="animate-fade-up">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-50 pb-2">Deskripsi Toko</h3>
                        <div class="prose prose-sm text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                            {{ $seller->description ?? 'Penjual belum menambahkan deskripsi toko.' }}
                        </div>
                    </div>

                    <div class="md:col-span-1 space-y-4">
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Detail Informasi</h4>
                            <ul class="space-y-3 text-xs">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-500">Bergabung:</span>
                                    <span
                                        class="font-bold text-gray-800 ml-auto">{{ $seller->created_at->format('d M Y') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-gray-500">Lokasi:</span>
                                    <span
                                        class="font-bold text-gray-800 ml-auto truncate max-w-[120px]">{{ $seller->address ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- MODAL DETAIL PRODUK --}}
        <div x-show="productModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 px-4 md:px-0"
            style="display: none;" x-cloak>
            <div x-show="productModal" @click="productModal = false"
                class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-transition.opacity></div>

            <div x-show="productModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[90vh] md:max-h-[600px]">

                {{-- Close Button --}}
                <button @click="productModal = false"
                    class="absolute top-4 right-4 z-20 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full backdrop-blur shadow-sm transition"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg></button>

                {{-- Left Image --}}
                <div class="w-full md:w-5/12 bg-gray-100 relative flex items-center justify-center shrink-0">
                    <img :src="getImageUrl(activeProduct?.image)" class="w-full h-full object-cover">
                    <div x-show="activeProduct?.discount > 0"
                        class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-lg font-bold text-sm shadow-md">
                        Hemat <span x-text="activeProduct?.discount"></span>%</div>
                </div>

                {{-- Right Info --}}
                <div class="w-full md:w-7/12 flex flex-col bg-white relative min-h-0">

                    {{-- Tombol Lapor Produk --}}
                    <button @click="openProductReport()"
                        class="absolute top-4 right-16 z-20 text-gray-400 hover:text-red-500 flex items-center gap-1 text-xs font-bold bg-gray-50 px-2 py-1.5 rounded-lg hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg> Laporkan
                    </button>

                    <div class="flex-1 p-6 md:p-8 overflow-y-auto custom-scroll">
                        <div class="flex items-center gap-3 mb-3">
                            <span
                                class="px-2.5 py-0.5 rounded-md bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wide"
                                x-text="activeProduct?.category?.name"></span>
                            <div class="flex items-center text-amber-400 text-xs gap-1">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-gray-700 font-bold text-sm"
                                    x-text="(parseFloat(activeProduct?.reviews_avg_rating) || 0).toFixed(1)"></span>
                                <span class="text-gray-400 font-normal"
                                    x-text="'(' + (activeProduct?.reviews_count || 0) + ' Ulasan)'"></span>
                            </div>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mb-2 leading-tight" x-text="activeProduct?.name"></h2>

                        <div class="flex items-end gap-3 mb-6 border-b border-gray-100 pb-6">
                            <h3 class="text-3xl font-black text-green-600"
                                x-text="formatCurrency(activeProduct?.price - (activeProduct?.price * (activeProduct?.discount / 100)))">
                            </h3>
                            <span x-show="activeProduct?.discount > 0" class="text-gray-400 line-through mb-1.5 text-sm"
                                x-text="formatCurrency(activeProduct?.price)"></span>
                        </div>

                        {{-- Tabs Switcher --}}
                        <div class="flex gap-6 mb-4 border-b border-gray-100">
                            <button @click="modalTab = 'desc'" class="pb-2 text-sm font-bold transition border-b-2"
                                :class="modalTab === 'desc' ? 'text-green-600 border-green-600' :
                                    'text-gray-400 border-transparent hover:text-gray-600'">Deskripsi</button>
                            <button @click="modalTab = 'reviews'" class="pb-2 text-sm font-bold transition border-b-2"
                                :class="modalTab === 'reviews' ? 'text-green-600 border-green-600' :
                                    'text-gray-400 border-transparent hover:text-gray-600'">Ulasan
                                Pembeli</button>
                        </div>

                        {{-- Tab Desc --}}
                        <div x-show="modalTab === 'desc'">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 mb-4">
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 shrink-0 bg-white flex items-center justify-center">
                                    <template x-if="activeProduct?.seller?.logo">
                                        <img :src="'{{ asset('') }}' + activeProduct?.seller?.logo"
                                            class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!activeProduct?.seller?.logo">
                                        <span class="font-bold text-gray-400 text-lg"
                                            x-text="activeProduct?.seller?.name.charAt(0)"></span>
                                    </template>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Penjual</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="activeProduct?.seller?.name"></p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"
                                x-text="activeProduct?.description"></p>
                        </div>

                        {{-- Tab Reviews --}}
                        <div x-show="modalTab === 'reviews'" class="space-y-4">
                            <template x-for="review in activeProduct?.reviews" :key="review.id">
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs font-bold text-gray-900"
                                            x-text="review.user?.name || 'Anonim'"></span>
                                        <div class="flex text-yellow-400 text-[10px]">
                                            <template x-for="i in 5"><svg class="w-2.5 h-2.5"
                                                    :class="i <= review.rating ? 'fill-current' : 'text-gray-300'"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg></template>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600" x-text="review.comment"></p>
                                </div>
                            </template>
                            <div x-show="!activeProduct?.reviews?.length"
                                class="text-center py-6 text-gray-400 text-xs italic">Belum ada ulasan.</div>
                        </div>
                    </div>

                    {{-- Footer Cart --}}
                    <div class="p-6 border-t border-gray-100 bg-white z-10 shadow-inner shrink-0">
                        {{-- FORM ADD CART MODAL --}}
                        <form action="{{ route('buyer.cart.store') }}" method="POST" class="flex gap-4">
                            @csrf
                            <input type="hidden" name="product_id" :value="activeProduct?.id">
                            <input type="hidden" name="quantity" :value="qty">

                            <div class="flex items-center border border-gray-300 rounded-xl h-12 px-2 w-32">
                                <button type="button" @click="qty > 1 ? qty-- : qty = 1"
                                    class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-green-600 font-bold text-lg">-</button>
                                <input type="text" x-model="qty"
                                    class="w-full text-center font-bold text-gray-900 border-none focus:ring-0 p-0 bg-transparent"
                                    readonly>
                                <button type="button" @click="qty < activeProduct?.stock ? qty++ : qty"
                                    class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-green-600 font-bold text-lg">+</button>
                            </div>
                            <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl h-12 shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                + Keranjang
                            </button>
                        </form>
                        <p class="text-center text-xs text-gray-400 mt-3">Stok Tersedia: <span
                                x-text="activeProduct?.stock"></span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. UNIFIED REPORT MODAL (SAMA PERSIS DI HOME) --}}
        <div x-show="reportModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;" x-cloak>
            {{-- Backdrop --}}
            <div x-show="reportModal" @click="reportModal = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm">
            </div>

            {{-- Content --}}
            <div x-show="reportModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden p-6">

                <div class="text-center mb-6">
                    <div
                        class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900"
                        x-text="reportTarget === 'product' ? 'Laporkan Produk' : 'Laporkan Toko'"></h3>
                    <p class="text-sm text-gray-500"
                        x-text="reportTarget === 'product' ? 'Mengapa Anda melaporkan produk ini?' : 'Apa masalah yang Anda temukan pada toko ini?'">
                    </p>
                </div>

                <form action="{{ route('buyer.reports.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <template x-if="reportTarget === 'product'">
                        <input type="hidden" name="product_id" :value="activeProduct?.id">
                    </template>
                    <template x-if="reportTarget === 'seller'">
                        <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                    </template>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alasan</label>
                        <select name="reason"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="">Pilih Alasan...</option>
                            <option value="Penipuan / Barang Palsu">Penipuan / Barang Palsu</option>
                            <option value="Produk Terlarang">Produk Terlarang</option>
                            <option value="Gambar / Konten Tidak Pantas">Gambar / Konten Tidak Pantas</option>
                            <option value="Pelayanan Buruk">Pelayanan Buruk / Kasar</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Detail</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none"
                            placeholder="Jelaskan lebih lanjut..."></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="reportModal = false"
                            class="flex-1 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg text-sm hover:bg-gray-200">Batal</button>
                        <button type="submit"
                            class="flex-1 py-2 bg-red-600 text-white font-bold rounded-lg text-sm hover:bg-red-700 shadow-lg">Kirim
                            Laporan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

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
