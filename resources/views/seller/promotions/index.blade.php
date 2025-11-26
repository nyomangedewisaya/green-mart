@extends('layouts.seller')
@section('title', 'Promosi Toko')

@section('content')

    <div x-data="{
        createModal: false,
        detailModal: false,
    
        detailPromo: null,
        imagePreview: null,
        searchQuery: '{{ request('search', '') }}',
        showImageError: false,
    
        startDate: '',
        endDate: '',
        totalDays: 0,
        totalPrice: 0,
        pricePerDay: 25000,
    
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },
    
        getImageUrl(path) {
            if (!path) return 'https://placehold.co/600x200/e0e0e0/757575?text=Banner+Promo';
            if (path.startsWith('http')) return path;
            return '{{ asset('') }}' + path;
        },
    
    
        submitCreate() {
            if (!this.imagePreview) {
                this.showImageError = true;
                return;
            }
            document.getElementById('formCreate').submit();
        },
    
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
                this.showImageError = false;
            }
        },
    
        openCreateModal() {
            this.createModal = true;
            this.imagePreview = null;
            this.startDate = '';
            this.endDate = '';
            this.calculatePrice();
            const form = document.getElementById('formCreate');
            if (form) form.reset();
        },
    
        calculatePrice() {
            if (this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    
                if (end < start) {
                    this.totalDays = 0;
                    this.totalPrice = 0;
                    return;
                }
    
                this.totalDays = diffDays;
                this.totalPrice = diffDays * this.pricePerDay;
            } else {
                this.totalDays = 0;
                this.totalPrice = 0;
            }
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.filterStatusSelect, { create: false, placeholder: 'Semua Status', onChange: () => $refs.filterForm.submit() });
        new TomSelect($refs.perPageSelect, { create: false, controlInput: null, onChange: () => $refs.filterForm.submit() });
    });" class="space-y-6 font-inter">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Promosi Toko</h1>
                <p class="text-sm text-gray-500 mt-1">Pasang iklan banner agar toko Anda lebih dikenal.</p>
            </div>

            <button @click="openCreateModal()"
                class="group flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition transform hover:-translate-y-0.5">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajukan Iklan Baru
            </button>
        </div>

        <div class="bg-white border border-green-100 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="bg-green-50 p-4 rounded-xl text-green-600 shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-base mb-2">Ketentuan Promosi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Tarif: <strong>Rp 25.000 / hari</strong>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Durasi Maksimal: <strong>30 Hari</strong>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Banner Wajib: <strong>Rasio 3:1 (Landscape)</strong>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3 italic">*Perubahan atau penghapusan iklan yang sudah diajukan hanya
                    dapat dilakukan oleh Admin, dan dana yang sudah dibayar tidak dapat dikembalikan.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
            <form action="{{ route('seller.promotions.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <div class="md:col-span-5">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" placeholder="Cari judul promosi..." x-model="searchQuery"
                                @keydown.enter.prevent="$refs.filterForm.submit()"
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 focus:outline-none focus:bg-white focus:ring-1 focus:ring-green-500 rounded-xl text-sm transition font-medium">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-2" x-show="searchQuery">
                                <button type="button"
                                    @click="searchQuery = ''; $nextTick(() => $refs.filterForm.submit());"
                                    class="p-1 text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" x-ref="filterStatusSelect">
                            <option value="all">Semua Status</option>
                            <option value="paid" @selected(request('status') == 'paid')>Aktif (Paid)</option>
                            <option value="pending" @selected(request('status') == 'pending')>Menunggu (Pending)</option>
                            <option value="expired" @selected(request('status') == 'expired')>Selesai (Expired)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tampil</label>
                        <select name="per_page" x-ref="perPageSelect">
                            @foreach ([6, 12, 24] as $opt)
                                <option value="{{ $opt }}" @selected(request('per_page', 6) == $opt)>{{ $opt }} Data
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <a href="{{ route('seller.promotions.index') }}"
                            class="w-full h-[42px] flex items-center justify-center bg-white border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-red-600 transition shadow-sm"
                            title="Reset Filter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="ml-2 text-sm font-bold">Reset</span>
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($promotions as $promo)
                <div
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden group flex flex-col h-full">
                    <div class="relative h-36 bg-gray-100 overflow-hidden cursor-pointer"
                        @click="detailModal = true; detailPromo = {{ $promo->toJson() }}">
                        <img :src="getImageUrl('{{ $promo->image }}')"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-700">

                        <div class="absolute top-3 right-3">
                            @if ($promo->status == 'paid')
                                <span
                                    class="px-2.5 py-1 bg-green-500 text-white text-[10px] font-bold rounded-lg shadow-sm uppercase tracking-wide">Aktif</span>
                            @elseif($promo->status == 'pending')
                                <span
                                    class="px-2.5 py-1 bg-yellow-400 text-white text-[10px] font-bold rounded-lg shadow-sm uppercase tracking-wide">Menunggu</span>
                            @else
                                <span
                                    class="px-2.5 py-1 bg-gray-500 text-white text-[10px] font-bold rounded-lg shadow-sm uppercase tracking-wide">Selesai</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 text-base line-clamp-1" title="{{ $promo->title }}">
                            {{ $promo->title }}
                        </h3>

                        <div class="mt-4 space-y-3 text-sm text-gray-500 flex-1">
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-800">Periode Tayang</p>
                                    <p class="text-xs">{{ $promo->start_date->locale('id')->translatedFormat('d M Y') }} -
                                        {{ $promo->end_date->locale('id')->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Total Biaya</p>
                                <p class="font-bold text-green-600">{{ number_format($promo->price, 0, ',', '.') }}</p>
                            </div>

                            <button @click="detailModal = true; detailPromo = {{ $promo->toJson() }}"
                                class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-lg text-xs font-bold transition">
                                Detail
                            </button>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-span-full py-12">
                    <div class="flex flex-col items-center justify-center text-center">

                        @if (request('search') || request('status'))
                            <div class="bg-gray-50 p-4 rounded-full mb-3 border border-gray-100 shadow-sm">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Promosi Tidak Ditemukan</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">
                                Tidak ada data promosi yang cocok dengan kata kunci "<span
                                    class="font-semibold text-gray-800">{{ request('search') ?? request('status') }}</span>".
                            </p>
                            <a href="{{ route('seller.promotions.index') }}"
                                class="mt-4 inline-flex items-center text-sm font-bold text-green-600 hover:text-green-700 hover:underline transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        @else
                            <div class="bg-green-50 p-5 rounded-full mb-4 border border-green-100 shadow-sm animate-pulse">
                                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Toko Anda Belum Punya Iklan</h3>
                            <p class="text-gray-500 mt-2 max-w-md mx-auto leading-relaxed">
                                Tingkatkan penjualan dengan menampilkan produk unggulan Anda di halaman utama.
                            </p>

                            <button @click="openCreateModal()"
                                class="mt-6 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5 flex items-center mx-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Pasang Iklan Sekarang
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $promotions->links() }}
        </div>

        <div x-show="createModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="createModal" @click="createModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="createModal"
                class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-green-600 p-6 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Ajukan Iklan Baru</h3>
                        <p class="text-xs">Tarif harian: <strong>Rp 25.000</strong></p>
                    </div>
                    <button @click="createModal = false" class="text-green-200 hover:text-white"><svg class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form id="formCreate" action="{{ route('seller.promotions.store') }}" method="POST"
                    enctype="multipart/form-data" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto custom-scroll">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Banner Iklan (3:1) <span
                                class="text-red-500">*</span></label>

                        <div class="group relative w-full h-36 bg-gray-100 rounded-xl border-2 border-dashed transition cursor-pointer flex items-center justify-center overflow-hidden"
                            :class="showImageError ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-green-500'"
                            @click="$refs.createImage.click()">

                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">

                            <div x-show="!imagePreview" class="text-center text-gray-400 group-hover:text-green-500"
                                :class="showImageError ? 'text-red-400' : ''">
                                <span class="text-xs font-bold">Klik untuk Upload Banner</span>
                                <p class="text-[10px] mt-1">Disarankan 1200x400 px</p>
                            </div>
                        </div>

                        <p x-show="showImageError" class="text-[10px] text-red-600 mt-1 font-bold flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Banner wajib diupload!
                        </p>

                        <input type="file" name="image" x-ref="createImage" class="hidden" accept="image/*"
                            @change="previewImage">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Judul Iklan</label>
                            <input type="text" name="title" required
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-xl focus:ring-1 focus:ring-green-500 outline-none text-sm"
                                placeholder="Contoh: Promo Spesial Akhir Tahun">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Link Tujuan
                                (Opsional)</label>
                            <input type="url" name="link"
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-xl focus:ring-1 focus:ring-green-500 outline-none text-sm"
                                placeholder="https://...">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Mulai</label>
                            <input type="date" name="start_date" x-model="startDate" @change="calculatePrice()"
                                required
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-xl focus:ring-1 focus:ring-green-500 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Selesai</label>
                            <input type="date" name="end_date" x-model="endDate" @change="calculatePrice()" required
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-xl focus:ring-1 focus:ring-green-500 outline-none text-sm">
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-xl p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-green-800 font-medium">Estimasi Biaya</p>
                            <p class="text-[10px] text-green-600 mt-0.5">
                                <span x-text="totalDays"></span> Hari x Rp 25.000
                            </p>
                            <p x-show="totalDays > 30" class="text-[10px] text-red-600 font-bold mt-1">Maksimal 30 Hari!
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-700" x-text="formatCurrency(totalPrice)"></p>
                        </div>
                    </div>

                    <button type="button" @click="submitCreate()" :disabled="totalDays > 30 || totalDays <= 0"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                        Ajukan Sekarang
                    </button>
                </form>
            </div>
        </div>


        <div x-show="detailModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="detailModal"
                class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="relative h-40 bg-gray-200">
                    <img :src="getImageUrl(detailPromo?.image)" class="w-full h-full object-cover">
                    <button @click="detailModal = false"
                        class="absolute top-3 right-3 bg-black/50 text-white p-1 rounded-full hover:bg-black/70"><svg
                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex justify-between items-start">
                        <h2 class="text-2xl font-bold text-gray-900 w-3/4 leading-tight" x-text="detailPromo?.title"></h2>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase tracking-wide"
                            x-text="detailPromo?.status"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs uppercase font-bold">Masa Tayang</p>
                            <p class="font-medium mt-1 text-gray-800">
                                <span x-text="formatDate(detailPromo?.start_date)"></span> -
                                <span x-text="formatDate(detailPromo?.end_date)"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase font-bold">Biaya</p>
                            <p class="font-bold mt-1 text-green-600 text-lg" x-text="formatCurrency(detailPromo?.price)">
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500 text-xs uppercase font-bold mb-1">Link Tautan</p>
                            <a :href="detailPromo?.link" target="_blank"
                                class="text-green-600 hover:underline truncate block"
                                x-text="detailPromo?.link || '-'"></a>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <p class="text-xs text-yellow-800 leading-relaxed">
                            <strong>Catatan:</strong> Jika status masih <em>Pending</em>, silakan tunggu konfirmasi admin.
                            Jika <em>Paid</em>, iklan sedang tayang.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
