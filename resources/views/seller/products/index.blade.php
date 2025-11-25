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
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        getImageUrl(path) {
            if (!path) return 'https://placehold.co/400x400/f3f4f6/9ca3af?text=No+Image';
            if (path.startsWith('http')) return path;
            return '{{ asset('') }}' + path;
        },
    
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
            }
        },
    
        openCreateModal() {
            this.createModal = true;
            this.imagePreview = null;
            const form = document.getElementById('formCreate');
            if (form) form.reset();
        },
    
        openEditModal(product, url) {
            this.editModal = true;
            this.editForm = product;
            this.editAction = url;
            this.imagePreview = this.getImageUrl(product.image);
        },
    
        openStatusModal(product, url) {
            this.statusModal = true;
            this.statusForm = {
                name: product.name,
                is_active: product.is_active
            };
            this.statusAction = url;
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" x-ref="filterStatusSelect">
                            <option value="">Semua Status</option>
                            <option value="active" @selected(request('status') == 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') == 'inactive')>Nonaktif</option>
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
                            class="h-[42px] w-[42px] flex-shrink-0 flex items-center justify-center bg-white border border-gray-300 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-red-600 transition"
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
                                        @click="detailModal = true; modalProduct = {{ $product->toJson() }}; modalProduct.category_name = '{{ $product->category->name }}';">
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
                                            <p
                                                class="font-bold text-gray-900 line-clamp-1 group-hover:text-green-600 transition">
                                                {{ $product->name }}</p>
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
                                        @click="openStatusModal({{ $product->toJson() }}, '{{ route('seller.products.update', $product) }}')"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border transition transform active:scale-95 hover:shadow-sm
                            {{ $product->is_active ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}"
                                        title="Klik untuk ubah status">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $product->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
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
                    class="absolute top-3 right-3 bg-black/50 text-white p-1 rounded-full md:hidden z-20"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg></button>

                <div class="w-full md:w-1/2 h-64 md:h-auto bg-gray-100 relative">
                    <img :src="getImageUrl(modalProduct?.image)" class="w-full h-full object-cover">

                    <div x-show="modalProduct?.discount > 0"
                        class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-lg font-bold text-sm shadow-lg">
                        <span x-text="modalProduct?.discount"></span>% OFF
                    </div>
                </div>

                <div class="w-full md:w-1/2 p-8 flex flex-col">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                                x-text="modalProduct?.category_name"></p>
                            <h2 class="text-2xl font-bold text-gray-900 leading-tight" x-text="modalProduct?.name"></h2>
                        </div>
                        <button @click="detailModal = false"
                            class="hidden md:block text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    <div class="mt-6 space-y-4 flex-1">
                        <div>
                            <p class="text-3xl font-bold text-green-600 tracking-tight"
                                x-text="formatCurrency(modalProduct?.price)"></p>
                            <p class="text-sm text-gray-500 mt-1" x-show="modalProduct?.stock > 0">Stok tersedia: <span
                                    class="font-bold text-gray-800" x-text="modalProduct?.stock"></span> unit</p>
                            <p class="text-sm text-red-500 font-bold mt-1" x-show="modalProduct?.stock <= 0">Stok Habis
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Deskripsi</h4>
                            <div class="text-sm text-gray-600 leading-relaxed h-32 overflow-y-auto custom-scroll pr-2">
                                <p x-text="modalProduct?.description"></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase"
                            :class="modalProduct?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            <span class="w-2 h-2 rounded-full mr-1.5"
                                :class="modalProduct?.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                            <span x-text="modalProduct?.is_active ? 'Publik' : 'Draft'"></span>
                        </span>
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

                            <div class="w-full lg:w-5/12 space-y-4">
                                <label class="block text-sm font-bold text-gray-700">Foto Utama</label>

                                <div class="relative aspect-square w-full bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 hover:border-green-500 transition-colors cursor-pointer group overflow-hidden flex items-center justify-center"
                                    @click="$refs.createImage.click()">

                                    <img x-show="imagePreview" :src="imagePreview"
                                        class="absolute inset-0 w-full h-full object-cover z-10">

                                    <div x-show="!imagePreview"
                                        class="text-center p-6 group-hover:scale-105 transition-transform duration-300">
                                        <div
                                            class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3 text-green-600">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-600">Upload Foto</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG/PNG, Max 2MB</p>
                                    </div>

                                    <div x-show="imagePreview"
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition z-20">
                                        <span
                                            class="text-white text-sm font-bold border border-white px-4 py-2 rounded-full">Ganti
                                            Foto</span>
                                    </div>
                                </div>
                                <input type="file" name="image" x-ref="createImage" class="hidden"
                                    accept="image/*" required @change="previewImage">

                                <div class="bg-green-50 p-3 rounded-xl border border-green-100 flex gap-3">
                                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-green-700 leading-relaxed">Pastikan foto produk jelas,
                                        pencahayaan cukup, dan berlatar bersih agar menarik pembeli.</p>
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
                        <button type="submit"
                            class="px-6 py-2.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-200 transform hover:-translate-y-0.5 text-sm">
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
                                            class="text-white text-sm font-bold border border-white px-4 py-2 rounded-full backdrop-blur-sm">Ganti
                                            Foto</span>
                                    </div>
                                </div>
                                <input type="file" name="image" x-ref="editImage" class="hidden" accept="image/*"
                                    @change="previewImage">

                                <p class="text-sm text-center font-bold text-gray-800" x-text="editForm.name"></p>

                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 flex gap-3">
                                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-blue-700 leading-relaxed">Pastikan foto produk jelas,
                                        pencahayaan cukup, dan berlatar bersih agar menarik pembeli.</p>
                                </div>
                            </div>

                            <div class="flex-1 space-y-6">

                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama
                                            Produk</label>
                                        <input type="text" name="name" x-model="editForm.name" required
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl text-sm transition font-medium text-gray-800">
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
                                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl text-sm transition font-bold text-gray-800">
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Stok</label>
                                        <input type="number" name="stock" x-model="editForm.stock" required
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl text-sm transition font-medium text-gray-800">
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
                                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl text-sm transition font-medium text-gray-800">
                                            <span class="absolute right-4 top-2.5 text-gray-400 font-bold text-sm">%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                                        <label class="flex items-center cursor-pointer relative">
                                            <input type="checkbox" name="is_active" value="1"
                                                :checked="editForm.is_active" class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                            <span class="ml-3 text-sm font-medium text-gray-700"
                                                x-text="editForm.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                                    <textarea name="description" x-model="editForm.description" rows="4" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl text-sm transition resize-none leading-relaxed text-gray-700"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-5 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10">
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
                            class="w-full inline-flex justify-center rounded-lg border border border-gray-300 shadow-sm px-4 py-2 bg-red-601 text-base font-medium text-white hover:bg-red-700 sm:text-sm">Ya,
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

                <form :action="statusAction" method="POST">
                    @csrf @method('PUT')

                    <input type="hidden" name="is_active" :value="statusForm.is_active ? 0 : 1">
                    <input type="hidden" name="update_type" value="status_toggle">

                    <div class="p-6 text-center">
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
                            Anda akan mengubah status produk <strong x-text="statusForm.name"
                                class="text-gray-800"></strong> menjadi
                            <span class="font-bold" :class="statusForm.is_active ? 'text-red-600' : 'text-green-600'"
                                x-text="statusForm.is_active ? 'NONAKTIF' : 'AKTIF'"></span>.
                        </p>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 flex flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border border-gray-300 shadow-sm px-4 py-2 text-base fon1-medium text-white focus:outline-none sm:text-sm transition"
                            :class="statusForm.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                            Ya, <span x-text="statusForm.is_active ? 'Nonaktifkan' : 'Aktifkan'"></span>
                        </button>
                        <button type="button" @click="statusModal = false"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
