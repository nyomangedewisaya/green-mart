@extends('layouts.buyer')
@section('title', 'Belanja Sayur & Buah Segar')

@section('content')

    {{-- WRAPPER UTAMA --}}
    <div x-data="{
        productModal: false,
        reportModal: false,
        activeProduct: null,
        qty: 1,
        activeTab: 'desc',
    
        openModal(product) {
            this.activeProduct = product;
            this.qty = 1;
            this.activeTab = 'desc';
            this.productModal = true;
        },
    
        openReport() {
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
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-24 right-4 z-[999] bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
                <div class="bg-white/20 p-1 rounded-full">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- 1. HERO BANNER (CINEMATIC CAROUSEL) --}}
        @if (isset($banners) && $banners->count() > 0)
            <div class="bg-white pb-6 pt-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{
                    activeSlide: 0,
                    slides: {{ $banners->count() }},
                    timer: null,
                    startTimer() { this.timer = setInterval(() => { this.activeSlide = this.activeSlide === this.slides - 1 ? 0 : this.activeSlide + 1 }, 6000) },
                    stopTimer() { clearInterval(this.timer) }
                }" x-init="startTimer()">

                    <div class="relative w-full rounded-3xl overflow-hidden shadow-2xl shadow-green-900/10 group h-[200px] sm:h-[350px] md:h-[420px]"
                        @mouseenter="stopTimer()" @mouseleave="startTimer()">

                        @foreach ($banners as $index => $banner)
                            <a href="{{ $banner->link ?? '#' }}"
                                class="absolute inset-0 transition-all duration-1000 ease-in-out"
                                x-show="activeSlide === {{ $index }}"
                                x-transition:enter="opacity-0 transform scale-105"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-100">
                                <img src="{{ asset($banner->image) }}" class="w-full h-full object-cover">
                                {{-- Gradient Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10">
                                </div>

                                {{-- Caption --}}
                                <div class="absolute bottom-8 left-8 md:left-12 right-8 text-white animate-slide-up">
                                    <h2 class="text-xl md:text-4xl font-black mb-2 shadow-sm leading-tight">
                                        {{ $banner->title }}</h2>
                                    <button
                                        class="px-4 py-2 bg-white/20 backdrop-blur-md border border-white/50 rounded-lg text-xs md:text-sm font-bold hover:bg-white hover:text-green-600 transition">
                                        Lihat Promo
                                    </button>
                                </div>
                            </a>
                        @endforeach

                        {{-- Dots & Progress --}}
                        <div class="absolute bottom-4 right-6 flex gap-2 z-10">
                            @foreach ($banners as $index => $banner)
                                <button @click="activeSlide = {{ $index }}"
                                    class="h-1.5 rounded-full transition-all duration-500 relative overflow-hidden bg-white/30"
                                    :class="activeSlide === {{ $index }} ? 'w-8' : 'w-2'">
                                    <div class="absolute inset-0 bg-white transition-all duration-[6000ms] ease-linear"
                                        :style="activeSlide === {{ $index }} ? 'width: 100%' : 'width: 0%'"></div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 2. STICKY FILTER --}}
        <div class="sticky top-20 z-30 bg-white/90 backdrop-blur-lg border-b border-gray-100 shadow-sm transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-3">

                    {{-- Kategori Scrollable --}}
                    <div class="flex-1 overflow-x-auto no-scrollbar pr-4 mask-fade-right">
                        <div class="flex gap-2">
                            {{-- Link Kategori dengan Anchor #products-section --}}
                            <a href="{{ route('buyer.home') }}#products-section"
                                class="shrink-0 px-4 py-2 rounded-full text-xs font-bold transition border flex items-center gap-2
                           {{ !request('category')
                               ? 'bg-green-600 text-white border-green-600 shadow-green-200 shadow-md'
                               : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600' }}">
                                🌱 Semua
                            </a>
                            @foreach ($categories as $cat)
                                <a href="{{ route('buyer.home', array_merge(request()->query(), ['category' => $cat->id])) }}#products-section"
                                    class="shrink-0 px-4 py-2 rounded-full text-xs font-bold transition border
                               {{ request('category') == $cat->id
                                   ? 'bg-green-600 text-white border-green-600 shadow-md'
                                   : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Divider & Sort --}}
                    <div class="flex shrink-0 items-center pl-4 border-l-2 border-gray-100 h-8 z-10">
                        <div class="relative group">
                            <select onchange="window.location.href = this.value"
                                class="appearance-none bg-transparent hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-lg focus:ring-0 block pl-2 pr-8 py-1 cursor-pointer transition-colors outline-none">
                                <option
                                    value="{{ route('buyer.home', array_merge(request()->query(), ['sort' => 'newest'])) }}#products-section"
                                    @selected(request('sort') == 'newest')>✨ Terbaru</option>
                                <option
                                    value="{{ route('buyer.home', array_merge(request()->query(), ['sort' => 'price_low'])) }}#products-section"
                                    @selected(request('sort') == 'price_low')>📉 Termurah</option>
                                <option
                                    value="{{ route('buyer.home', array_merge(request()->query(), ['sort' => 'price_high'])) }}#products-section"
                                    @selected(request('sort') == 'price_high')>📈 Termahal</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. PRODUCT GRID (SCROLL REVEAL) --}}
        <div id="products-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-[60vh] scroll-mt-32">

            @if ($products->count() > 0)
                <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4 animate-on-load">
                    <p class="text-gray-600 text-sm font-medium">
                        Menampilkan <span class="font-bold text-gray-900">{{ $products->total() }}</span> produk segar
                        pilihan
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 sm:gap-6">
                    @foreach ($products as $index => $product)
                        <div
                            class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-green-200 transition-all duration-500 flex flex-col overflow-hidden relative reveal-on-scroll">

                            {{-- BADGES --}}
                            <div class="absolute top-2 left-2 z-20 flex flex-col gap-1.5 items-start">
                                @if ($product->is_featured)
                                    <div
                                        class="bg-amber-400 text-white text-[9px] font-bold px-2 py-1 rounded-md shadow-sm flex items-center gap-1">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Pilihan
                                    </div>
                                @endif
                                @if ($product->discount > 0)
                                    <div class="bg-red-500 text-white text-[9px] font-bold px-2 py-1 rounded-md shadow-sm">
                                        Hemat {{ $product->discount }}%</div>
                                @endif
                            </div>

                            {{-- WISHLIST BUTTON (FIXED ANCHOR SCROLL) --}}
                            <div class="absolute top-2 right-2 z-20">
                                @auth
                                    <form action="{{ route('buyer.wishlist.toggle') }}#products-section" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit"
                                            class="flex items-center justify-center w-8 h-8 bg-white/90 backdrop-blur-md rounded-full shadow-sm border border-gray-100 hover:scale-110 active:scale-95 transition group/heart"
                                            title="Wishlist">
                                            <svg class="w-4 h-4 {{ $product->is_wishlisted ? 'text-red-500 fill-current' : 'text-gray-400 fill-transparent group-hover/heart:text-red-400' }} transition-colors"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('auth.login') }}"
                                        class="flex items-center justify-center w-8 h-8 bg-white/90 backdrop-blur-md rounded-full shadow-sm border border-gray-100 hover:scale-110 transition">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </a>
                                @endauth
                            </div>

                            {{-- IMAGE --}}
                            <div class="relative aspect-square bg-gray-50 overflow-hidden cursor-pointer"
                                @click="openModal({{ \Illuminate\Support\Js::from($product->load('category', 'reviews.user', 'seller')) }})">
                                <img src="{{ $product->image ? asset($product->image) : 'https://placehold.co/300?text=Produk' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-700 mix-blend-multiply"
                                    loading="lazy">

                                <div
                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-end justify-center p-3 opacity-0 group-hover:opacity-100">
                                    <button
                                        class="w-full py-2 bg-white/90 backdrop-blur text-green-700 font-bold text-xs rounded-xl shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                        Lihat Detail
                                    </button>
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

                                <div class="mt-auto flex items-end justify-between pt-2 border-t border-gray-50">
                                    <div>
                                        @if ($product->discount > 0)
                                            <p class="text-[9px] text-gray-400 line-through mb-0.5">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @endif
                                        @php $final = $product->price - ($product->price * ($product->discount / 100)); @endphp
                                        <p class="text-sm font-black text-green-600">Rp
                                            {{ number_format($final, 0, ',', '.') }}</p>
                                    </div>

                                    {{-- ADD TO CART --}}
                                    @auth
                                        <form action="{{ route('buyer.cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit"
                                                class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 hover:bg-green-600 hover:text-white hover:border-green-600 transition shadow-sm group/cart">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Jika Guest: Redirect ke Login --}}
                                        <a href="{{ route('auth.login') }}"
                                            class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-green-600 hover:text-white transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12">{{ $products->fragment('products-section')->links() }}</div>
            @else
                <div class="py-24 text-center reveal-on-scroll">
                    <div class="bg-gray-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4"><svg
                            class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg></div>
                    <h3 class="text-lg font-bold text-gray-900">Tidak Ada Produk</h3>
                    <p class="text-gray-500 text-sm mt-2">Silakan cari dengan kata kunci lain.</p>
                </div>
            @endif
        </div>

        {{-- 4. BOTTOM SECTION (AGAR TIDAK KOSONG) --}}
        <div class="bg-white border-t border-gray-100 py-16 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Mengapa Green Mart?</h2>
                    <p class="text-gray-500 text-sm">Kami menghubungkan Anda langsung dengan sumber terbaik.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="p-6 rounded-2xl bg-green-50/50 border border-green-50 text-center hover:-translate-y-1 transition duration-300">
                        <div
                            class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-4 text-green-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Kualitas Terjamin</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">Produk dipilih langsung dari petani dan produsen
                            lokal terpercaya dengan standar kesegaran tinggi.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-blue-50/50 border border-blue-50 text-center hover:-translate-y-1 transition duration-300">
                        <div
                            class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-4 text-blue-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Pengiriman Cepat</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">Layanan pengiriman instan dan sameday untuk
                            menjaga produk tetap segar sampai di tangan Anda.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-amber-50/50 border border-amber-50 text-center hover:-translate-y-1 transition duration-300">
                        <div
                            class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-4 text-amber-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Dukung Lokal</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">Setiap pembelian Anda membantu memberdayakan
                            ekonomi petani dan pelaku UMKM lokal.</p>
                    </div>
                </div>

                <div class="mt-16 bg-gray-900 rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-green-500 rounded-full blur-3xl opacity-20 -mr-16 -mt-16">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20 -ml-16 -mb-16">
                    </div>

                    <div class="relative z-10">
                        <h2 class="text-2xl md:text-3xl font-black text-white mb-4">Ingin Berjualan di Green Mart?</h2>
                        <p class="text-gray-400 text-sm md:text-base max-w-xl mx-auto mb-8">Buka toko Anda sekarang dan
                            jangkau ribuan pelanggan potensial. Gratis biaya pendaftaran!</p>
                        <a href="#"
                            class="inline-block px-8 py-3.5 bg-green-600 hover:bg-green-500 text-white font-bold rounded-full shadow-lg shadow-green-900/50 transition transform hover:-translate-y-1">
                            Daftar Sebagai Seller
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. PRODUCT DETAIL MODAL --}}
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
                    <button @click="openReport()"
                        class="absolute top-4 right-16 z-20 text-gray-400 hover:text-red-500 flex items-center gap-1 text-xs font-bold bg-gray-50 px-2 py-1.5 rounded-lg hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg> Laporkan
                    </button>

                    <div class="flex-1 p-6 md:p-8 overflow-y-auto custom-scroll">
                        {{-- ... (Konten Info Produk Sama Seperti Sebelumnya) ... --}}
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

                        {{-- Tabs --}}
                        <div class="flex gap-6 mb-4 border-b border-gray-100">
                            <button @click="activeTab = 'desc'" class="pb-2 text-sm font-bold transition border-b-2"
                                :class="activeTab === 'desc' ? 'text-green-600 border-green-600' :
                                    'text-gray-400 border-transparent hover:text-gray-600'">Deskripsi</button>
                            <button @click="activeTab = 'reviews'" class="pb-2 text-sm font-bold transition border-b-2"
                                :class="activeTab === 'reviews' ? 'text-green-600 border-green-600' :
                                    'text-gray-400 border-transparent hover:text-gray-600'">Ulasan
                                Pembeli</button>
                        </div>

                        {{-- Tab Desc --}}
                        <div x-show="activeTab === 'desc'">
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
                                <a :href="'{{ url('buyer/shop') }}/' + activeProduct?.seller?.slug"
                                    class="ml-auto text-xs font-bold text-green-600 border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-50 transition no-underline">Kunjungi
                                    Toko</a>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"
                                x-text="activeProduct?.description"></p>
                        </div>

                        {{-- Tab Reviews --}}
                        <div x-show="activeTab === 'reviews'" class="space-y-4">
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
                                class="text-center py-6 text-gray-400 text-xs italic">Belum ada ulasan untuk produk ini.
                            </div>
                        </div>
                    </div>

                    {{-- Fixed Footer --}}
                    <div class="p-6 border-t border-gray-100 bg-white z-10 shadow-inner shrink-0">
                        @auth
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
                        @else
                            <a href="{{ route('auth.login') }}"
                                class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-green-600 hover:text-white transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </a>
                        @endauth
                        <p class="text-center text-xs text-gray-400 mt-3">Stok Tersedia: <span
                                x-text="activeProduct?.stock"></span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. REPORT MODAL --}}
        <div x-show="reportModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4"
            style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="reportModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 animate-fade-up">
                <div class="text-center mb-6">
                    <div
                        class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Laporkan Produk</h3>
                </div>
                <form action="{{ route('buyer.reports.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" :value="activeProduct?.id">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alasan</label>
                        <select name="reason"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                            <option value="Barang Palsu">Barang Palsu / KW</option>
                            <option value="Produk Terlarang">Produk Terlarang</option>
                            <option value="Gambar Tidak Pantas">Gambar Tidak Pantas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Detail</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm" placeholder="Jelaskan..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="reportModal = false"
                            class="flex-1 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg text-sm">Batal</button>
                        <button type="submit"
                            class="flex-1 py-2 bg-red-600 text-white font-bold rounded-lg text-sm hover:bg-red-700 shadow-lg">Kirim</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- SCRIPT & STYLE UNTUK ANIMASI SCROLL --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('show');
                        observer.unobserve(entry.target); // Hanya animasi sekali
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

    <style>
        /* Animation Classes */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal-on-scroll.show {
            opacity: 1;
            transform: translateY(0);
        }

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

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.8s ease-out forwards;
        }
    </style>

@endsection
