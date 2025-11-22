@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')

    <div x-data="{
        manageModal: false,
        detailModal: false,
        modalProduct: null,
        modalActionUrl: '',
        rejectionNotes: '',
        searchQuery: '{{ request('search', '') }}',
    
        formatCurrency(value) {
            if (isNaN(value)) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.categorySelect, {
            create: false,
            placeholder: 'Pilih Kategori',
            onChange: () => $refs.filterForm.submit()
        });
    
        new TomSelect($refs.sellerSelect, {
            create: false,
            placeholder: 'Pilih Seller',
            onChange: () => $refs.filterForm.submit()
        });
    
        new TomSelect($refs.statusSelect, {
            create: false,
            placeholder: 'Pilih Status',
            onChange: () => $refs.filterForm.submit()
        });
    
        new TomSelect($refs.perPageSelect, {
            create: false,
            controlInput: null,
            onChange: () => $refs.filterForm.submit()
        });
    
    });">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800">Manajemen Produk</h1>
                <p class="text-gray-500 mt-1">Moderasi dan kelola semua produk dari seller.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.products.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>

                            <input type="text" id="search" name="search" placeholder="Ketik nama produk..."
                                x-model="searchQuery" @keydown.enter.prevent="$refs.filterForm.submit()"
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none transition-all">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                <button :type="searchQuery ? 'button' : 'submit'"
                                    @click="if (searchQuery) { 
                                    searchQuery = ''; 
                                    $nextTick(() => $refs.filterForm.submit());
                                }"
                                    class="p-1 text-gray-400 rounded-full transition-all"
                                    :class="{
                                        'hover:text-red-600 hover:bg-red-100': searchQuery,
                                        'hover:text-green-600 hover:bg-green-100': !searchQuery
                                    }"
                                    :title="searchQuery ? 'Bersihkan pencarian' : 'Cari'">

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

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" x-ref="statusSelect" class="w-full" x-cloak>
                            <option value=""></option>
                            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                            <option value="live" @selected(request('status') == 'live')>Live</option>
                        </select>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" x-ref="categorySelect" class="w-full" x-cloak>
                            <option value=""></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="seller_id" class="block text-sm font-medium text-gray-700 mb-1">Seller</label>
                        <select name="seller_id" x-ref="sellerSelect" class="w-full" x-cloak>
                            <option value=""></option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->id }}" @selected(request('seller_id') == $seller->id)>{{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Tampil</label>
                        <select name="per_page" x-ref="perPageSelect" class="w-full" x-cloak>
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>
                                    {{ $option }} data
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-3">
                        <a href="{{ route('admin.products.index') }}"
                            class="h-10 px-5 py-5.5 flex items-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Seller</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Unggulan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($products as $index => $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm w-10">
                                    <span
                                        class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 font-medium border border-gray-300 shadow-sm">
                                        {{ $products->firstItem() + $index }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900 flex items-center">
                                    <button type="button"
                                        @click="detailModal = true; modalProduct = {{ $product->toJson() }};"
                                        class="flex items-center text-left group">
                                        <img src="{{ $product->image ?? 'https://placehold.co/60x60/e0e0e0/757575?text=N/A' }}"
                                            alt="{{ $product->name }}"
                                            class="w-15 h-15 rounded-md object-cover mr-3 shrink-0">
                                        <span
                                            class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition truncate w-40">{{ $product->name }}</span>
                                    </button>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $product->seller->name ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">Rp
                                    {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @if ($product->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Live
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    <form action="{{ route('admin.products.update', $product) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="toggle_feature">
                                        <button type="submit"
                                            class="p-1 rounded-full transition-colors {{ $product->is_featured ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}"
                                            title="{{ $product->is_featured ? 'Hapus dari Unggulan' : 'Jadikan Unggulan' }}">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    <button
                                        @click="
                                        manageModal = true;
                                        modalProduct = {{ $product->toJson() }};
                                        modalActionUrl = '{{ route('admin.products.update', $product) }}';
                                        rejectionNotes = '{{ $product->admin_notes }}';
                                    "
                                        class="text-gray-600 hover:text-green-800 transition transform hover:-translate-y-0.5"
                                        title="Manage Produk">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        @if (request('search') || request('status') || request('seller_id'))
                                            <div class="bg-gray-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Promosi Tidak Ditemukan</h3>
                                            <p class="text-sm text-gray-500 mt-1">Tidak ada data promosi yang cocok dengan
                                                filter Anda.</p>
                                        @else
                                            <div class="bg-blue-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.43.816 1.035.816 1.73 0 .695-.32 1.3-.816 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Belum Ada Iklan</h3>
                                            <p class="text-sm text-gray-500 mt-1">Seller belum mengajukan promosi atau
                                                iklan apapun.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>


        <div x-show="manageModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">

            <div x-show="manageModal" @click="manageModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="manageModal" @click.away="manageModal = false"
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-red-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white" x-text="modalProduct?.name">Detail Produk</h2>
                    <button type="button" @click="manageModal = false"
                        class="text-white hover:text-gray-300 transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="modalActionUrl" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-4 max-h-60vh overflow-y-auto">
                        <div>
                            <h3 class="font-medium text-gray-800">Deskripsi</h3>
                            <p class="text-sm text-gray-600 mt-1" x-text="modalProduct?.description"></p>
                        </div>

                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Admin
                                (Alasan Penolakan/Suspend)</label>
                            <textarea name="admin_notes" x-model="rejectionNotes"
                                class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-red-500 focus:outline-none transition-all"
                                rows="3" placeholder="Tulis alasan jika produk ditolak..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">

                        <template x-if="modalProduct && !modalProduct.is_active">
                            <div>
                                <button type="submit" name="action" value="reject"
                                    class="px-5 py-2.5 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition">
                                    Reject
                                </button>
                                <button type="submit" name="action" value="approve"
                                    class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition">
                                    Approve
                                </button>
                            </div>
                        </template>

                        <template x-if="modalProduct && modalProduct.is_active">
                            <button type="submit" name="action" value="suspend"
                                class="px-5 py-2.5 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition">
                                Suspend
                            </button>
                        </template>

                    </div>
                </form>
            </div>
        </div>
        <div x-show="detailModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">

            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="detailModal" @click.away="detailModal = false"
                class="relative w-full max-w-3xl bg-white rounded-xl shadow-2xl"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-green-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white">Detail Produk</h2>
                    <button type="button" @click="detailModal = false"
                        class="text-green-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto" x-show="modalProduct">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <img :src="modalProduct?.image || 'https://placehold.co/300x300/e0e0e0/757575?text=N/A'"
                                :alt="modalProduct?.name"
                                class="w-full h-auto rounded-lg object-cover shadow-md border border-gray-200">
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-3xl font-bold text-gray-900" x-text="modalProduct?.name"></h3>
                            <p class="text-lg font-semibold text-green-600 mt-2"
                                x-text="formatCurrency(modalProduct?.price)"></p>

                            <div class="flex items-center space-x-2 mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': modalProduct?.is_active,
                                        'bg-red-100 text-red-800': !modalProduct?.is_active
                                    }">
                                    <span x-text="modalProduct?.is_active ? 'Live' : 'Pending'"></span>
                                </span>
                                <span x-show="modalProduct?.is_featured"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Unggulan
                                </span>
                            </div>

                            <dl class="mt-6 space-y-4">
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="w-2/3 text-sm text-gray-900"
                                        x-text="modalProduct?.category?.name || 'N/A'"></dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Seller</dt>
                                    <dd class="w-2/3 text-sm text-gray-900" x-text="modalProduct?.seller?.name || 'N/A'">
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Stok</dt>
                                    <dd class="w-2/3 text-sm text-gray-900" x-text="modalProduct?.stock"></dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Diskon</dt>
                                    <dd class="w-2/3 text-sm text-gray-900"
                                        x-text="(modalProduct?.discount && modalProduct?.discount > 0) ? modalProduct.discount + '%' : 'Tidak ada diskon'">
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Tanggal Upload</dt>
                                    <dd class="w-2/3 text-sm text-gray-900" x-text="formatDate(modalProduct?.created_at)">
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800">Deskripsi Produk</h4>
                        <p class="text-sm text-gray-600 mt-2" x-text="modalProduct?.description"></p>
                    </div>

                    <div x-show="modalProduct?.admin_notes"
                        class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h4 class="text-lg font-semibold text-red-800">Catatan Admin</h4>
                        <p class="text-sm text-red-700 mt-2" x-text="modalProduct?.admin_notes"></p>
                    </div>

                </div>

                <div class="flex justify-end space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                    <button type="button" @click="detailModal = false"
                        class="px-5 py-2.5 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
</div> @endsection
