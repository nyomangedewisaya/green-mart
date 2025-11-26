@extends('layouts.seller')
@section('title', 'Produk Saya')

@section('content')

    <div x-data="{
        createModal: false,
        editModal: false,
        deleteModal: false,
        detailModal: false,
        statusModal: false,
    
        modalProduct: null,
        editForm: {},
        deleteForm: { title: '' },
        statusForm: { id: null, name: '', is_active: false },
    
        editAction: '',
        deleteAction: '',
        statusAction: '',
    
        imagePreview: null,
        searchQuery: '{{ request('search', '') }}',
        showImageError: false,
    
        productReviews: [],
        filteredReviews: [],
        reviewStats: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0, total: 0, avg: 0 },
        currentReviewFilter: 'all',
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        getImageUrl(path) {
            if (!path) return 'https://placehold.co/400x400/f3f4f6/9ca3af?text=No+Image';
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
            const form = document.getElementById('formCreate');
            if (form) form.reset();
        },
    
        openDetailModal(product) {
            this.detailModal = true;
            this.modalProduct = product;
            this.modalProduct.category_name = product.category ? product.category.name : '-';
            this.productReviews = product.reviews || [];
            this.calculateReviewStats();
            this.filterReviews('all');
        },
    
        openStatusModal(product, url) {
            this.statusModal = true;
            this.statusForm = {
                id: product.id,
                name: product.name,
                is_active: product.is_active,
                is_suspended: !!product.admin_notes,
                admin_note: product.admin_notes
            };
            this.statusAction = url;
        },
    
        openDetailModal(product) {
            this.detailModal = true;
            this.modalProduct = product;
            this.productReviews = product.reviews || [];
            this.calculateReviewStats();
            this.filterReviews('all');
        },
    
        calculateReviewStats() {
            let stats = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0, total: 0, sum: 0, avg: 0 };
    
            this.productReviews.forEach(r => {
                stats[r.rating]++;
                stats.total++;
                stats.sum += r.rating;
            });
    
            if (stats.total > 0) {
                stats.avg = (stats.sum / stats.total).toFixed(1);
            }
            this.reviewStats = stats;
        },
    
        filterReviews(star) {
            this.currentReviewFilter = star;
            if (star === 'all') {
                this.filteredReviews = this.productReviews;
            } else {
                this.filteredReviews = this.productReviews.filter(r => r.rating == star);
            }
        }
    
    }" x-init="$nextTick(() => {
        new TomSelect($refs.filterCategorySelect, { create: false, placeholder: 'Semua Kategori', onChange: () => $refs.filterForm.submit() });
        new TomSelect($refs.filterStatusSelect, { create: false, placeholder: 'Semua Status', onChange: () => $refs.filterForm.submit() });
        new TomSelect($refs.perPageSelect, { create: false, controlInput: null, onChange: () => $refs.filterForm.submit() });
    
        $watch('createModal', (value) => {
            if (value) {
                $nextTick(() => {
                    if (!this._ts_create_cat) this._ts_create_cat = new TomSelect($refs.createCategorySelect, { create: false, placeholder: 'Pilih Kategori...' });
                    else this._ts_create_cat.clear();
                });
            }
        });
    
        $watch('editModal', (value) => {
            if (value) {
                $nextTick(() => {
                    if (!this._ts_edit_cat) this._ts_edit_cat = new TomSelect($refs.editCategorySelect, { create: false, placeholder: 'Pilih Kategori...' });
                    this._ts_edit_cat.setValue(this.editForm.category_id);
                });
            }
        });
    });" class="space-y-6 font-inter">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Produk</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola katalog dan stok barang dagangan Anda.</p>
            </div>
            <button @click="openCreateModal()"
                class="group flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </button>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('seller.products.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>

                            <input type="text" name="search" placeholder="Nama produk..." x-model="searchQuery"
                                @keydown.enter.prevent="$refs.filterForm.submit()"
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-1 focus:ring-green-500 focus:outline-none transition-all">

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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" x-ref="filterCategorySelect">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" x-ref="filterStatusSelect">
                            <option value="">Semua</option>
                            <option value="active" @selected(request('status') == 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') == 'inactive')>Nonaktif</option>

                            <option value="suspended" @selected(request('status') == 'suspended')>Ditangguhkan (Admin)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end gap-2">
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tampil</label>
                            <select name="per_page" x-ref="perPageSelect">
                                @foreach ([10, 25, 50] as $opt)
                                    <option value="{{ $opt }}" @selected(request('per_page') == $opt)>{{ $opt }}
                                        Data</option>
                                @endforeach
                            </select>
                        </div>

                        <a href="{{ route('seller.products.index') }}"
                            class="h-[42px] w-[42px] shrink-0 flex items-center justify-center bg-white border border-gray-300 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-red-600 transition"
                            title="Reset Filter">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-100 font-medium">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Harga & Stok</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $index => $product)
                            <tr class="hover:bg-gray-50/80 transition group">

                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 font-bold border border-gray-200 text-xs shadow-sm">
                                        {{ $products->firstItem() + $index }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center cursor-pointer"
                                        @click="openDetailModal({{ json_encode($product) }})">
                                        <div
                                            class="relative w-12 h-12 rounded-lg overflow-hidden border border-gray-200 mr-4 group-hover:border-green-400 transition shrink-0">
                                            <img :src="getImageUrl('{{ $product->image }}')"
                                                class="w-full h-full object-cover">
                                            @if ($product->discount > 0)
                                                <div
                                                    class="absolute bottom-0 left-0 right-0 bg-red-500 text-white text-[8px] font-bold text-center py-0.5">
                                                    {{ $product->discount }}%
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div>
                                                <p
                                                    class="font-bold text-gray-900 line-clamp-1 group-hover:text-green-600 transition">
                                                    {{ $product->name }}</p>

                                                @if ($product->admin_notes)
                                                    <p class="text-[10px] text-red-600 font-bold flex items-center mt-0.5">
                                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                        Disuspend Admin
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-600 border border-green-200 whitespace-nowrap">
                                        {{ $product->category->name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 whitespace-nowrap"
                                            x-text="formatCurrency({{ $product->price }})"></span>
                                        <span
                                            class="text-xs {{ $product->stock <= 5 ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                            Stok: {{ $product->stock }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                        @click="openStatusModal({{ $product->toJson() }}, '{{ route('seller.products.update', $product->slug) }}')"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border transition transform active:scale-95 hover:shadow-sm
                                        {{ $product->admin_notes
                                            ? 'bg-red-100 text-red-700 border-red-200 cursor-not-allowed opacity-75'
                                            : ($product->is_active
                                                ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'
                                                : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200') }}"
                                        title="{{ $product->admin_notes ? 'Produk ditangguhkan' : 'Klik untuk ubah status' }}">

                                        @if ($product->admin_notes)
                                            <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Ditangguhkan
                                        @else
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $product->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        @endif
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="openEditModal({{ $product->toJson() }}, '{{ route('seller.products.update', $product->slug) }}')"
                                            class="p-2 text-blue-600 bg-white border border-blue-200 hover:bg-blue-50 rounded-lg transition shadow-sm"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteModal = true; deleteForm.title = '{{ $product->name }}'; deleteAction = '{{ route('seller.products.destroy', $product->slug) }}';"
                                            class="p-2 text-red-600 bg-white border border-red-200 hover:bg-red-50 rounded-lg transition shadow-sm"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        @if (request('search') || request('category_id') || request('status'))
                                            <div class="bg-gray-50 p-4 rounded-full mb-3 border border-gray-100">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-900">Produk Tidak Ditemukan</h3>
                                            <p class="text-xs text-gray-500 mt-1 max-w-xs">Tidak ada produk yang cocok
                                                dengan filter atau kata kunci pencarian Anda.</p>
                                            <a href="{{ route('seller.products.index') }}"
                                                class="mt-3 text-xs font-bold text-green-600 hover:underline">Reset
                                                Filter</a>
                                        @else
                                            <div class="bg-blue-50 p-4 rounded-full mb-3 border border-blue-100">
                                                <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-900">Belum Ada Produk</h3>
                                            <p class="text-xs text-gray-500 mt-1">Toko Anda masih kosong. Yuk mulai
                                                berjualan!</p>
                                            <button @click="openCreateModal()"
                                                class="mt-3 px-4 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition">Tambah
                                                Produk Sekarang</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">{{ $products->links() }}</div>
        </div>

        <div x-show="detailModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="detailModal"
                class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                <button @click="detailModal = false"
                    class="absolute top-4 right-4 z-20 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex flex-col md:flex-row h-full">

                    <div
                        class="w-full md:w-5/12 bg-gray-50 border-r border-gray-200 overflow-y-auto custom-scroll relative">
                        <div class="relative aspect-square w-full">
                            <img :src="getImageUrl(modalProduct?.image)" class="w-full h-full object-cover">

                            <div
                                class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full shadow-lg flex items-center gap-2 border border-gray-100">
                                <svg class="w-5 h-5 text-red-500 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                                <span class="font-bold text-gray-800 text-sm"><span
                                        x-text="modalProduct?.wishlists_count || 0"></span> Peminat</span>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded uppercase"
                                    x-text="modalProduct?.category?.name"></span>
                                <h2 class="text-2xl font-bold text-gray-900 mt-2 leading-tight"
                                    x-text="modalProduct?.name"></h2>
                            </div>

                            <div class="flex justify-between items-center border-y border-gray-200 py-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Harga</p>
                                    <p class="text-xl font-bold text-gray-900"
                                        x-text="formatCurrency(modalProduct?.price)"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 uppercase font-bold">Stok</p>
                                    <p class="text-lg font-medium text-gray-900" x-text="modalProduct?.stock"></p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">Deskripsi</h4>
                                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap"
                                    x-text="modalProduct?.description"></p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-7/12 bg-white flex flex-col h-full">

                        <div class="p-6 border-b border-gray-100 bg-white z-10">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Ulasan Pembeli
                            </h3>

                            <div class="mt-4 flex items-center gap-6">
                                <div class="text-center">
                                    <span class="text-4xl font-bold text-gray-900" x-text="reviewStats.avg"></span>
                                    <div class="flex text-yellow-400 text-sm justify-center mt-1">★★★★★</div>
                                    <p class="text-xs text-gray-400 mt-1"><span x-text="reviewStats.total"></span> Ulasan
                                    </p>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <template x-for="star in [5, 4, 3, 2, 1]">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="w-3 font-bold text-gray-500" x-text="star"></span>
                                            <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-yellow-400 rounded-full"
                                                    :style="'width: ' + (reviewStats.total > 0 ? (reviewStats[star] /
                                                        reviewStats.total * 100) : 0) + '%'">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-6 overflow-x-auto no-scrollbar pb-1">
                                <button @click="filterReviews('all')"
                                    class="px-3 py-1 rounded-full text-xs font-bold border transition"
                                    :class="currentReviewFilter === 'all' ? 'bg-green-600 text-white border-green-600' :
                                        'bg-white text-gray-600 border-gray-200 hover:border-green-500'">Semua</button>
                                <template x-for="star in [5,4,3,2,1]">
                                    <button @click="filterReviews(star)"
                                        class="px-3 py-1 rounded-full text-xs font-bold border transition flex items-center whitespace-nowrap"
                                        :class="currentReviewFilter === star ? 'bg-green-600 text-white border-green-600' :
                                            'bg-white text-gray-600 border-gray-200 hover:border-green-500'">
                                        <span x-text="star"></span> <svg class="w-3 h-3 ml-1 mb-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-4 bg-gray-50/30">
                            <template x-for="review in filteredReviews" :key="review.id">
                                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 overflow-hidden">
                                                <img x-show="getUserAvatar(review.user?.avatar)"
                                                    :src="getUserAvatar(review.user?.avatar)"
                                                    class="w-full h-full object-cover">
                                                <span x-show="!getUserAvatar(review.user?.avatar)"
                                                    x-text="review.user?.name?.substring(0,1) || 'U'"></span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-900"
                                                    x-text="review.user?.name || 'Pengguna'"></p>
                                                <p class="text-[10px] text-gray-400"
                                                    x-text="formatDate(review.created_at)"></p>
                                            </div>
                                        </div>
                                        <div class="flex text-yellow-400 text-xs">
                                            <template x-for="i in 5">
                                                <svg class="w-3 h-3"
                                                    :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200'"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed" x-text="review.comment"></p>
                                </div>
                            </template>
                            <div x-show="filteredReviews.length === 0" class="text-center py-8 text-gray-400">
                                <p class="text-sm">Belum ada ulasan.</p>
                            </div>
                        </div>

                        <div class="p-4 border-t border-gray-200 bg-white text-right">
                            <button @click="detailModal = false"
                                class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-bold transition">Tutup</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div x-show="createModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="createModal" @click="createModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="createModal"
                class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div
                    class="flex justify-between items-center px-8 py-5 bg-green-600 border-b border-gray-100 sticky top-0 z-10">
                    <div>
                        <h3 class="text-xl font-bold text-white">Tambah Produk</h3>
                        <p class="text-xs text-white mt-0.5">Lengkapi detail produk baru Anda.</p>
                    </div>
                    <button @click="createModal = false" class="text-white hover:text-gray-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="formCreate" action="{{ route('seller.products.store') }}" method="POST"
                    enctype="multipart/form-data" class="flex-1 overflow-y-auto custom-scroll">
                    @csrf
                    <div class="p-8">
                        <div class="flex flex-col lg:flex-row gap-8">

                            <div class="w-full sm:w-1/3">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Foto
                                    Produk <span class="text-red-500">*</span></label>

                                <div class="group relative w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed transition cursor-pointer flex items-center justify-center overflow-hidden"
                                    :class="showImageError ? 'border-red-500 bg-red-50' :
                                        'border-gray-300 hover:border-green-500'"
                                    @click="$refs.createImage.click()">

                                    <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">

                                    <div x-show="!imagePreview"
                                        class="text-center text-gray-400 group-hover:text-green-500"
                                        :class="showImageError ? 'text-red-400' : ''">
                                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs">Upload</span>
                                    </div>
                                </div>

                                <p x-show="showImageError"
                                    class="text-[10px] text-red-600 mt-1 font-bold flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Wajib upload foto produk!
                                </p>

                                <input type="file" name="image" x-ref="createImage" class="hidden"
                                    accept="image/*" @change="previewImage">

                                <div class="mt-3 bg-green-50 p-3 rounded-xl border border-green-100 flex gap-3">
                                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-green-700 leading-relaxed">Pastikan foto produk jelas,
                                        pencahayaan cukup, dan berlatar bersih.</p>
                                </div>
                            </div>

                            <div class="flex-1 space-y-6">

                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama
                                            Produk</label>
                                        <input type="text" name="name" required
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 rounded-xl text-sm transition font-medium text-gray-800 placeholder-gray-400"
                                            placeholder="Contoh: Apel Fuji Premium 1kg">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                                        <select name="category_id" x-ref="createCategorySelect" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <hr class="border-gray-100">

                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Harga
                                            (Rp)</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-2.5 text-gray-400 font-bold text-sm">Rp</span>
                                            <input type="number" name="price" required
                                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 rounded-xl text-sm transition font-bold text-gray-800"
                                                placeholder="0">
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Stok
                                            Awal</label>
                                        <input type="number" name="stock" required
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 rounded-xl text-sm transition font-medium text-gray-800"
                                            placeholder="0">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-5 items-center">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Diskon
                                            (%)</label>
                                        <div class="relative">
                                            <input type="number" name="discount" min="0" max="100"
                                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 rounded-xl text-sm transition font-medium text-gray-800"
                                                placeholder="0">
                                            <span class="absolute right-4 top-2.5 text-gray-400 font-bold text-sm">%</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status
                                            Produk</label>
                                        <label class="flex items-center cursor-pointer relative">
                                            <input type="checkbox" name="is_active" value="1" checked
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                                            </div>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Langsung Aktif</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi
                                        Lengkap</label>
                                    <textarea name="description" rows="4" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 rounded-xl text-sm transition resize-none leading-relaxed text-gray-700"
                                        placeholder="Jelaskan detail, spesifikasi, dan keunggulan produk Anda..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-5 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10">
                        <button type="button" @click="createModal = false"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm text-sm">
                            Batal
                        </button>
                        <button type="button" @click="submitCreate()"
                            class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-md">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editModal" class="fixed inset-0 z-100 flex items-center justify-center p-4" style="display: none;">
            <div x-show="editModal" @click="editModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="editModal"
                class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div
                    class="flex justify-between items-center px-8 py-5 bg-blue-600 border-b border-gray-100 sticky top-0 z-10">
                    <h3 class="text-xl font-bold text-white">Edit Produk</h3>
                    <button @click="editModal = false" class="text-white hover:text-gray-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="editAction" method="POST" enctype="multipart/form-data"
                    class="flex-1 overflow-y-auto custom-scroll">
                    @csrf @method('PUT')

                    <div class="px-8 pt-6 pb-0" x-show="editForm.admin_notes">
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm flex gap-4 items-start">
                            <div class="p-2 bg-white rounded-full text-red-500 shadow-sm shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-red-800 uppercase tracking-wide">Produk Ditangguhkan
                                    Admin</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Alasan: <span class="font-medium italic bg-red-100 px-1 rounded"
                                        x-text="editForm.admin_notes"></span>
                                </p>
                                <p class="text-xs text-red-600 mt-2 bg-white/60 p-2 rounded border border-red-100">
                                    Silakan perbaiki data produk (foto, nama, deskripsi) sesuai catatan di atas, lalu
                                    simpan. Hubungi admin jika perbaikan sudah dilakukan.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex flex-col lg:flex-row gap-8">

                            <div class="w-full lg:w-5/12 space-y-4">
                                <label class="block text-sm font-bold text-gray-700">Foto Produk</label>

                                <div class="relative aspect-square w-full bg-gray-50 rounded-2xl border-2 border-dashed border-blue-200 hover:border-blue-500 transition-colors cursor-pointer group overflow-hidden flex items-center justify-center shadow-inner"
                                    @click="$refs.editImage.click()">

                                    <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover z-10">

                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition z-20">
                                        <span
                                            class="text-white text-sm font-bold border border-white px-4 py-2 rounded-full backdrop-blur-sm shadow-lg">Ganti
                                            Foto</span>
                                    </div>
                                </div>
                                <input type="file" name="image" x-ref="editImage" class="hidden" accept="image/*"
                                    @change="previewImage">

                                <p class="text-sm text-center font-bold text-gray-800 truncate" x-text="editForm.name">
                                </p>

                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 flex gap-3">
                                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-blue-700 leading-relaxed">Pastikan foto produk jelas,
                                        pencahayaan cukup, dan berlatar bersih.</p>
                                </div>
                            </div>

                            <div class="flex-1 space-y-6">

                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama
                                            Produk</label>
                                        <input type="text" name="name" x-model="editForm.name" required
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl text-sm transition font-medium text-gray-800 shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                                        <select name="category_id" x-ref="editCategorySelect"
                                            :value="editForm.category_id">
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <hr class="border-gray-100">

                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Harga
                                            (Rp)</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-2.5 text-gray-400 font-bold text-sm">Rp</span>
                                            <input type="number" name="price" x-model="editForm.price" required
                                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl text-sm transition font-bold text-gray-800 shadow-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Stok</label>
                                        <input type="number" name="stock" x-model="editForm.stock" required
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl text-sm transition font-medium text-gray-800 shadow-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-5 items-center">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Diskon
                                            (%)</label>
                                        <div class="relative">
                                            <input type="number" name="discount" x-model="editForm.discount"
                                                min="0" max="100"
                                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl text-sm transition font-medium text-gray-800 shadow-sm">
                                            <span class="absolute right-4 top-2.5 text-gray-400 font-bold text-sm">%</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                                        <label class="flex items-center relative transition"
                                            :class="editForm.admin_notes ? 'cursor-not-allowed opacity-60 grayscale' :
                                                'cursor-pointer'">

                                            <input type="checkbox" name="is_active" value="1"
                                                :checked="editForm.is_active" class="sr-only peer"
                                                :disabled="editForm.admin_notes">

                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>

                                            <span class="ml-3 text-sm font-medium text-gray-700"
                                                x-text="editForm.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                        </label>
                                        <p x-show="editForm.admin_notes"
                                            class="text-[10px] text-red-500 mt-1 font-bold ml-1">*Status dikunci Admin</p>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                                    <textarea name="description" x-model="editForm.description" rows="4" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl text-sm transition resize-none leading-relaxed text-gray-700 shadow-sm"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div
                        class="px-8 py-5 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                        <button type="button" @click="editModal = false"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 transform hover:-translate-y-0.5 text-sm">
                            Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="deleteModal" class="fixed inset-0 z-100 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="deleteModal" @click="deleteModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            <div class="relative w-full max-w-sm bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <form :action="deleteAction" method="POST">
                    @csrf @method('DELETE')
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4"><svg
                                class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg></div>
                        <h3 class="text-lg font-bold text-gray-900">Hapus Produk?</h3>
                        <p class="text-sm text-gray-500 mt-2">Produk "<strong x-text="deleteForm.title"></strong>" akan
                            dihapus permanen.</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 flex flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-red-300 shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:text-sm">Ya,
                            Hapus</button>
                        <button type="button" @click="deleteModal = false"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="statusModal" class="fixed inset-0 z-110 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="statusModal" @click="statusModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="statusModal" class="relative w-full max-w-sm bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="p-6 text-center">

                    <div x-show="statusForm.is_suspended">
                        <div
                            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 animate-pulse">
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Akses Ditolak</h3>
                        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                            Produk ini sedang <strong>ditangguhkan oleh Admin</strong>. <br>
                            Alasan: <span class="text-red-600 font-medium" x-text="statusForm.admin_note"></span>
                        </p>
                    </div>

                    <div x-show="!statusForm.is_suspended">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 transition-colors"
                            :class="statusForm.is_active ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'">
                            <svg x-show="statusForm.is_active" class="w-8 h-8" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <svg x-show="!statusForm.is_active" class="w-8 h-8" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900"
                            x-text="statusForm.is_active ? 'Nonaktifkan Produk?' : 'Aktifkan Produk?'"></h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Ubah status menjadi <span class="font-bold"
                                :class="statusForm.is_active ? 'text-red-600' : 'text-green-600'"
                                x-text="statusForm.is_active ? 'NONAKTIF' : 'AKTIF'"></span>?
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 flex flex-row-reverse gap-2">
                    <button type="submit" x-show="!statusForm.is_suspended"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:text-sm transition"
                        :class="statusForm.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                        Ya, Ubah
                    </button>

                    <button type="button" @click="statusModal = false"
                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                        <span x-text="statusForm.is_suspended ? 'Tutup' : 'Batal'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
