@extends('layouts.buyer')
@section('title', 'Mitra Toko & Petani')

@section('content')

    {{-- 1. HERO SECTION (Modern Gradient) --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-green-600 to-teal-700 text-white">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute -bottom-10 -right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute top-0 left-0 w-64 h-64 bg-yellow-400/20 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10 text-center">
            <span
                class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-xs font-bold uppercase tracking-widest mb-4 backdrop-blur-md text-green-50">
                Mitra Resmi Green Mart
            </span>
            <h1 class="text-3xl md:text-5xl font-black mb-4 tracking-tight">
                Jelajahi Mitra UMKM & Toko Lokal
            </h1>
            <p class="text-green-100 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                Temukan beragam produk berkualitas langsung dari pengusaha mikro dan toko pilihan.
                Mari dukung pertumbuhan ekonomi lokal dengan belanja produk asli UMKM.
            </p>

            {{-- SEARCH BAR BESAR --}}
            {{-- SEARCH BAR BESAR --}}
            <div class="mt-10 max-w-2xl mx-auto">
                {{-- PERBAIKAN: Tambahkan #sellers-content di akhir route --}}
                <form action="{{ route('buyer.sellers.index') }}#sellers-content" method="GET" class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama toko, lokasi, atau produk..."
                        class="w-full pl-7 pr-16 py-4 rounded-full bg-white text-gray-800 font-medium shadow-2xl shadow-green-900/20 border-2 border-transparent focus:border-green-300 focus:ring-0 transition-all outline-none placeholder-gray-400">

                    <button type="submit"
                        class="absolute right-2 top-2 bottom-2 bg-green-600 hover:bg-green-700 text-white px-6 rounded-full transition font-bold shadow-md flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="sellers-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen font-inter scroll-mt-24">

        {{-- FILTER TOOLBAR --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4 animate-fade-in">
            <p class="text-gray-500 font-medium text-sm">
                Menampilkan <span class="font-bold text-gray-900">{{ $sellers->total() }}</span> mitra aktif
            </p>

            <div class="flex flex-wrap items-center gap-3">

                {{-- Filter Rating --}}
                <div class="relative group">
                    <select onchange="window.location.href = this.value"
                        class="appearance-none bg-white border border-gray-200 hover:border-green-400 text-gray-700 text-xs font-bold rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 block pl-4 pr-10 py-2.5 cursor-pointer transition shadow-sm">
                        {{-- PERBAIKAN: Tambahkan #sellers-content di setiap value option --}}
                        <option value="{{ route('buyer.sellers.index', request()->except('rating')) }}#sellers-content">⭐
                            Semua Rating</option>
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['rating' => 4.5])) }}#sellers-content"
                            @selected(request('rating') == '4.5')>⭐ 4.5 Ke atas</option>
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['rating' => 4])) }}#sellers-content"
                            @selected(request('rating') == '4')>⭐ 4.0 Ke atas</option>
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['rating' => 3])) }}#sellers-content"
                            @selected(request('rating') == '3')>⭐ 3.0 Ke atas</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                {{-- Sorting --}}
                <div class="relative group">
                    <select onchange="window.location.href = this.value"
                        class="appearance-none bg-white border border-gray-200 hover:border-green-400 text-gray-700 text-xs font-bold rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 block pl-4 pr-10 py-2.5 cursor-pointer transition shadow-sm">
                        {{-- PERBAIKAN: Tambahkan #sellers-content di setiap value option --}}
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['sort' => 'newest'])) }}#sellers-content"
                            @selected(request('sort') == 'newest')>✨ Terbaru</option>
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['sort' => 'top_rated'])) }}#sellers-content"
                            @selected(request('sort') == 'top_rated')>🌟 Rating Tertinggi</option>
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['sort' => 'products_count'])) }}#sellers-content"
                            @selected(request('sort') == 'products_count')>📦 Produk Terbanyak</option>
                        <option
                            value="{{ route('buyer.sellers.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}#sellers-content"
                            @selected(request('sort') == 'oldest')>📅 Terlama</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRID SELLERS --}}
        @if ($sellers->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($sellers as $index => $seller)
                    @php
                        $rating = number_format($seller->reviews_avg_rating ?? 0, 1);
                        // Logic warna rating
                        $ratingColor =
                            $rating >= 4.5 ? 'text-amber-400' : ($rating >= 4.0 ? 'text-yellow-400' : 'text-gray-300');
                    @endphp

                    <a href="{{ route('buyer.sellers.show', $seller->slug) }}"
                        class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-500 flex flex-col overflow-hidden relative animate-fade-up"
                        style="animation-delay: {{ $index * 75 }}ms">

                        {{-- Banner (Backdrop) --}}
                        <div class="h-28 bg-gray-100 overflow-hidden relative">
                            <img src="{{ $seller->banner ? asset($seller->banner) : 'https://placehold.co/600x200/10b981/ffffff?text=Store' }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60 group-hover:opacity-80 transition">
                            </div>

                            {{-- Rating Badge di Banner --}}
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-xs font-bold text-gray-800">{{ $rating }}</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="px-5 pb-6 pt-0 flex-1 flex flex-col relative">

                            {{-- Floating Logo --}}
                            <div class="relative -mt-10 mb-3 flex justify-between items-end">
                                <div class="relative">
                                    <img src="{{ $seller->logo ? asset($seller->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($seller->name) . '&background=10b981&color=fff' }}"
                                        class="w-20 h-20 rounded-2xl border-4 border-white shadow-md object-cover bg-white group-hover:rotate-3 transition transform">

                                    @if ($seller->is_verified)
                                        <div class="absolute -bottom-1 -right-1 bg-blue-500 text-white p-1 rounded-full border-2 border-white shadow-sm"
                                            title="Terverifikasi">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Name & Info --}}
                            <div class="mb-4">
                                <h3
                                    class="text-lg font-bold text-gray-900 leading-tight group-hover:text-green-600 transition line-clamp-1 mb-1">
                                    {{ $seller->name }}</h3>
                                <div class="flex items-center gap-1 text-gray-500 text-xs">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate">{{ $seller->address ?? 'Lokasi tidak tersedia' }}</span>
                                </div>
                            </div>

                            {{-- Stats Bar --}}
                            <div class="mt-auto pt-4 border-t border-gray-50 flex justify-between items-center">
                                <div
                                    class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg group-hover:bg-green-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span
                                        class="text-xs font-bold text-gray-600 group-hover:text-green-700">{{ $seller->products_count }}
                                        <span
                                            class="font-normal text-gray-400 group-hover:text-green-600">Produk</span></span>
                                </div>

                                <div
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-green-600 group-hover:text-white transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $sellers->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <div class="py-24 text-center animate-fade-in">
                {{-- ... Icon ... --}}
                <h3 class="text-xl font-bold text-gray-900">Toko Tidak Ditemukan</h3>
                <p class="text-gray-500 mt-2 max-w-xs mx-auto">Maaf, kami tidak menemukan toko dengan kriteria tersebut.</p>
                {{-- PERBAIKAN: Reset juga mengarah ke anchor content --}}
                <a href="{{ route('buyer.sellers.index') }}" class="mt-6 inline-block px-6 py-2 bg-white border border-gray-300 text-gray-600 font-bold rounded-full hover:bg-gray-50 transition shadow-sm text-sm">
                    Reset Pencarian
                </a>
            </div>
        @endif

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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>

@endsection
