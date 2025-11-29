@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')

    <div x-data="{
        createModal: false,
        editModal: false,
        deleteModal: false,
        editForm: { id: null, name: '' },
        editAction: '',
        deleteForm: { name: '' },
        deleteAction: '',
        searchQuery: '{{ request('search', '') }}'
    }" x-init="new TomSelect($refs.sortSelect, {
        create: false,
        controlInput: null,
        onChange: (value) => {
            $refs.filterForm.submit();
        }
    });">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800">Manajemen Kategori</h1>
                <p class="text-gray-500 mt-1">Kelola semua kategori produk untuk Green Mart.</p>
            </div>
            <button @click="createModal = true"
                class="mt-4 sm:mt-0 flex items-center justify-center px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-md hover:bg-green-700 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Kategori
            </button>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.categories.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Kategori</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>

                            <input type="text" id="search" name="search" placeholder="Ketik nama kategori..."
                                x-model="searchQuery"
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
                        <label for="sort-select" class="block text-sm font-medium text-gray-700 mb-1">Urutkan
                            Berdasarkan</label>

                        <select id="sort-select" name="sort" x-ref="sortSelect" class="hidden">
                            @php
                                $sortValue = request('sort', 'latest');
                            @endphp
                            <option value="latest" @selected($sortValue == 'latest')>Tanggal Dibuat (Terbaru)</option>
                            <option value="oldest" @selected($sortValue == 'oldest')>Tanggal Dibuat (Terlama)</option>
                            <option value="name_asc" @selected($sortValue == 'name_asc')>Nama (A - Z)</option>
                            <option value="name_desc" @selected($sortValue == 'name_desc')>Nama (Z - A)</option>
                            <option value="products_high" @selected($sortValue == 'products_high')>Jumlah Produk (Tertinggi)</option>
                            <option value="products_low" @selected($sortValue == 'products_low')>Jumlah Produk (Terendah)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full min-w-lg">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Jumlah Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($categories as $index => $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm">
                                    <span
                                        class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 font-medium border border-gray-300 shadow-sm">
                                        {{ $categories->firstItem() + $index }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-500 font-mono">
                                    <span class="bg-gray-100 py-0.5 px-1 rounded-sm">{{ $category->slug }}</span>
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-700">
                                    <span
                                        class="bg-green-100 text-green-600 font-medium rounded-full shadow-sm py-0.5 px-2">{{ $category->products_count }}
                                        Produk</span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium space-x-3">
                                    <button
                                        @click="
                                        editModal = true;
                                        editForm.id = {{ $category->id }};
                                        editForm.name = '{{ $category->name }}';
                                        editAction = '{{ route('admin.categories.update', $category) }}';
                                        "
                                        class="text-blue-600 hover:text-blue-800 transition transform hover:-translate-y-0.5"
                                        title="Edit">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button <button
                                        @click="
                                            deleteModal = true;
                                            deleteForm.name = '{{ $category->name }}';
                                            deleteAction = '{{ route('admin.categories.destroy', $category) }}';
                                        "
                                        class="text-red-600 hover:text-red-800 transition transform hover:-translate-y-0.5"
                                        title="Hapus">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        @if (request('search'))
                                            <div class="bg-gray-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Kategori Tidak Ditemukan</h3>
                                            <p class="text-sm text-gray-500 mt-1">Tidak ada kategori yang cocok dengan
                                                keyword "<span
                                                    class="font-medium text-gray-700">{{ request('search') }}</span>".</p>
                                        @else
                                            <div class="bg-green-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.737.513l8.26-3.304c1.122-.448 1.631-1.878.897-2.835L17.5 3.707c-.554-.554-1.23-.857-1.95-.857H9.568z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Belum Ada Kategori</h3>
                                            <p class="text-sm text-gray-500 mt-1">Mulailah dengan menambahkan kategori
                                                produk baru.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        </div>


        <div x-show="createModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">

            <div x-show="createModal" @click="createModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="createModal" @click.away="createModal = false"
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-green-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white">Tambah Kategori Baru</h2>
                    <button type="button" @click="createModal = false"
                        class="text-green-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="name_create" class="block text-sm font-medium text-gray-700 mb-1">Nama
                                Kategori</label>
                            <input type="text" id="name_create" name="name" required
                                placeholder="Contoh: Sayuran Segar"
                                class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:shadow-lg focus:shadow-green-500/20 focus:outline-none transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="createModal = false"
                            class="px-5 py-2.5 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">

            <div x-show="editModal" @click="editModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="editModal" @click.away="editModal = false"
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-blue-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white">Edit Kategori</h2>
                    <button type="button" @click="editModal = false" class="text-blue-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="name_edit" class="block text-sm font-medium text-gray-700 mb-1">Nama
                                Kategori</label>
                            <input type="text" id="name_edit" name="name" required x-model="editForm.name"
                                class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:shadow-lg focus:shadow-blue-500/20 focus:outline-none transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="editModal = false"
                            class="px-5 py-2.5 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="deleteModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">

            <div x-show="deleteModal" @click="deleteModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="deleteModal" @click.away="deleteModal = false"
                class="relative w-full max-w-md bg-white rounded-xl shadow-2xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">

                <form :action="deleteAction" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex flex-col items-center justify-center p-6 bg-red-600 rounded-t-xl">
                        <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20">
                            <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                            </svg>
                        </span>
                    </div>

                    <div class="p-6 text-center">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Hapus Kategori?</h2>
                        <p class="text-gray-600 ">
                            Anda yakin ingin menghapus kategori <br>
                            "<strong x-text="deleteForm.name"></strong>"?
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>

                    <div class="flex justify-center space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="deleteModal = false"
                            class="px-5 py-2.5 w-full bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 w-full bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition">
                            Ya, Hapus
                            </E>
                    </div>
                </form>
            </div>
        </div>

</div> @endsection
