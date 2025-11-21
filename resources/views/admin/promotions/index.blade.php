@extends('layouts.admin')
@section('title', 'Manajemen Promosi')
@section('content')
    <div x-data="{
        createModal: false,
        editModal: false,
        deleteModal: false,
        detailModal: false,
        statusModal: false,
        editForm: {},
        detailPromo: null,
        statusForm: { title: '', action: '' },
        deleteForm: { title: '' },
        editAction: '',
        deleteAction: '',
        imagePreview: null,
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
                month: 'short',
                year: 'numeric'
            });
        },
    
        formatDateForInput(dateString) {
            if (!dateString) return '';
            return dateString.split('T')[0];
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.filterSellerSelect, {
            create: false,
            placeholder: 'Pilih Seller',
            onChange: () => { $refs.filterForm.submit(); }
        });
    
        new TomSelect($refs.filterStatusSelect, {
            create: false,
            placeholder: 'Pilih Status',
            onChange: () => { $refs.filterForm.submit(); }
        });
    
        new TomSelect($refs.perPageSelect, {
            create: false,
            controlInput: null,
            onChange: () => { $refs.filterForm.submit(); }
        });
    });
    
    $watch('createModal', (value) => {
        if (value) {
            $nextTick(() => {
                new TomSelect($refs.createSellerSelect, {
                    create: false,
                    placeholder: 'Pilih Seller...'
                });
                new TomSelect($refs.createStatusSelect, {
                    create: false,
                    placeholder: 'Pilih Status...'
                });
            });
        }
    });
    
    $watch('editModal', (value) => {
        if (value) {
            $nextTick(() => {
                new TomSelect($refs.editSellerSelect, {
                    create: false,
                    placeholder: 'Pilih Seller...'
                });
                new TomSelect($refs.editStatusSelect, {
                    create: false,
                    placeholder: 'Pilih Status...'
                });
            });
        }
    });">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800">Manajemen Promosi</h1>
                <p class="text-gray-500 mt-1">Kelola banner iklan dan slot promosi dari seller.</p>
            </div>
            <button @click="createModal = true; clearPreview();"
                class="mt-4 sm:mt-0 flex items-center justify-center px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-md hover:bg-green-700 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Promosi
            </button>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.promotions.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Judul
                            Promosi</label>
                        <div class="relative">
                            <input type="text" id="search" name="search" placeholder="Ketik judul..."
                                x-model="searchQuery" @keydown.enter.prevent="$refs.filterForm.submit()"
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none transition-all">

                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>

                            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                <button :type="searchQuery ? 'button' : 'submit'"
                                    @click="if (searchQuery) { searchQuery = ''; $nextTick(() => $refs.filterForm.submit()); }"
                                    class="p-1 text-gray-400 rounded-full transition-all"
                                    :class="{
                                        'hover:text-red-600 hover:bg-red-100': searchQuery,
                                        'hover:text-green-600 hover:bg-green-100':
                                            !searchQuery
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
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                        <select name="status" x-ref="filterStatusSelect" class="w-full" x-cloak>
                            <option value=""></option>
                            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                            <option value="paid" @selected(request('status') == 'paid')>Paid</option>
                            <option value="expired" @selected(request('status') == 'expired')>Expired</option>
                        </select>
                    </div>

                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Tampil</label>
                        <select name="per_page" x-ref="perPageSelect" class="w-full" x-cloak>
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>
                                    {{ $option }} per halaman
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="seller_id" class="block text-sm font-medium text-gray-700 mb-1">Seller</label>
                        <select name="seller_id" x-ref="filterSellerSelect" class="w-full" x-cloak>
                            <option value=""></option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->id }}" @selected(request('seller_id') == $seller->id)>{{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-3">
                        <a href="{{ route('admin.promotions.index') }}"
                            class="h-10 shrink-0 px-5 py-5.5 flex items-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
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
                                Promosi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Seller</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Masa Aktif</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aktif</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($promotions as $index => $promo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm w-10">
                                    <span
                                        class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 font-medium border border-gray-300 shadow-sm">
                                        {{ $promotions->firstItem() + $index }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <button type="button"
                                        @click="detailModal = true; detailPromo = {{ $promo->toJson() }};"
                                        class="flex items-center text-left group">
                                        <img src="{{ $promo->image ? asset($promo->image) : 'https://placehold.co/128x80/e0e0e0/757575?text=16:9' }}"
                                            alt="{{ $promo->title }}"
                                            class="w-22 h-16 rounded-md object-cover mr-3 shrink-0">
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition truncate w-40">
                                                {{ $promo->title }}</p>
                                            <a href="{{ $promo->link }}" target="_blank"
                                                class="text-xs text-gray-500 hover:text-green-600 truncate w-40"
                                                @click.stop>
                                                {{ $promo->link }}
                                            </a>
                                        </div>
                                    </button>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $promo->seller->name ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ $promo->start_date->translatedFormat('d M Y') }} -
                                    {{ $promo->end_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">Rp
                                    {{ number_format($promo->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @if ($promo->status == 'pending')
                                        <button
                                            @click="
                                            statusModal = true;
                                            statusForm.title = '{{ $promo->title }}';
                                            statusForm.action = '{{ route('admin.promotions.update', $promo) }}';
                                        "
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition"
                                            title="Ubah Status Pembayaran">
                                            Pending
                                        </button>
                                    @elseif ($promo->status == 'paid')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Paid
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <form action="{{ route('admin.promotions.update', $promo) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="toggle_active">
                                        <button type="submit"
                                            class="w-10 h-6 rounded-full p-0.5 transition-colors duration-200 ease-in-out {{ $promo->is_active ? 'bg-green-600' : 'bg-gray-200' }}"
                                            title="{{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <span
                                                class="block w-5 h-5 rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out {{ $promo->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium space-x-2">
                                    <button
                                        @click="
                                        editModal = true;
                                        editForm = {{ $promo->toJson() }};
                                        editAction = '{{ route('admin.promotions.update', $promo) }}';
                                        imagePreview = '{{ $promo->image ? asset($promo->image) : 'https://placehold.co/128x80/e0e0e0/757575?text=16:9' }}';
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
                                    <button
                                        @click="
                                        deleteModal = true;
                                        deleteForm.title = '{{ $promo->title }}';
                                        deleteAction = '{{ route('admin.promotions.destroy', $promo) }}';
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
                {{ $promotions->links() }}
            </div>
        </div>


        <div x-show="createModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="createModal" @click="createModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>
            <div x-show="createModal" @click.away="createModal = false"
                class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-green-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white">Tambah Promosi Baru</h2>
                    <button type="button" @click="createModal = false"
                        class="text-green-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banner Promosi (Wajib)</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <img :src="imagePreview || 'https://placehold.co/128x80/e0e0e0/757575?text=16:9'"
                                    alt="Image Preview" class="w-32 h-20 rounded-lg object-cover bg-gray-100 shadow-sm">
                                <input type="file" name="image" @change="previewImage" accept="image/*"
                                    class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-green-50 file:text-green-700
                                hover:file:bg-green-100 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="title_create" class="block text-sm font-medium text-gray-700 mb-1">Judul
                                    Promosi</label>
                                <input type="text" id="title_create" name="title" required
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="link_create" class="block text-sm font-medium text-gray-700 mb-1">Link URL
                                    (Opsional)</label>
                                <input type="url" id="link_create" name="link" placeholder="https://..."
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="seller_create" class="block text-sm font-medium text-gray-700 mb-1">Seller
                                (Pemilik Iklan)</label>
                            <select id="seller_create" name="seller_id" required x-ref="createSellerSelect">
                                <option value=""></option>
                                @foreach ($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="price_create" class="block text-sm font-medium text-gray-700 mb-1">Harga Iklan
                                    (Rp)</label>
                                <input type="number" id="price_create" name="price" required value="0"
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="status_create" class="block text-sm font-medium text-gray-700 mb-1">Status
                                    Pembayaran</label>
                                <select id="status_create" name="status" x-ref="createStatusSelect">
                                    <option value="pending" selected>Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date_create"
                                    class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" id="start_date_create" name="start_date" required
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="end_date_create" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                    Berakhir</label>
                                <input type="date" id="end_date_create" name="end_date" required
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                    class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                                <span class="text-sm font-medium text-gray-700">Aktifkan promosi ini sekarang?</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="createModal = false"
                            class="px-5 py-2.5 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition">
                            Simpan Promosi
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
                class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-blue-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white">Edit Promosi</h2>
                    <button type="button" @click="editModal = false" class="text-blue-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="editAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banner Promosi (Ganti jika
                                perlu)</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <img :src="imagePreview" alt="Image Preview"
                                    class="w-32 h-20 rounded-lg object-cover bg-gray-100 shadow-sm">
                                <input type="file" name="image" @change="previewImage" accept="image/*"
                                    class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="title_edit" class="block text-sm font-medium text-gray-700 mb-1">Judul
                                    Promosi</label>
                                <input type="text" id="title_edit" name="title" required x-model="editForm.title"
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="link_edit" class="block text-sm font-medium text-gray-700 mb-1">Link URL
                                    (Opsional)</label>
                                <input type="url" id="link_edit" name="link" placeholder="https://..."
                                    x-model="editForm.link"
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="seller_edit" class="block text-sm font-medium text-gray-700 mb-1">Seller (Pemilik
                                Iklan)</label>
                            <select id="seller_edit" name="seller_id" required x-ref="editSellerSelect"
                                :value="editForm.seller_id">
                                @foreach ($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="price_edit" class="block text-sm font-medium text-gray-700 mb-1">Harga Iklan
                                    (Rp)</label>
                                <input type="number" id="price_edit" name="price" required x-model="editForm.price"
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="status_edit" class="block text-sm font-medium text-gray-700 mb-1">Status
                                    Pembayaran</label>
                                <select id="status_edit" name="status" x-ref="editStatusSelect"
                                    :value="editForm.status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date_edit" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                    Mulai</label>
                                <input type="date" id="start_date_edit" name="start_date" required
                                    :value="formatDateForInput(editForm.start_date)"
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="end_date_edit" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                    Berakhir</label>
                                <input type="date" id="end_date_edit" name="end_date" required
                                    :value="formatDateForInput(editForm.end_date)"
                                    class="w-full pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                    class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                    :checked="editForm.is_active">
                                <span class="text-sm font-medium text-gray-700">Aktifkan promosi ini?</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="editModal = false"
                            class="px-5 py-2.5 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
                            Perbarui Promosi
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
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Hapus Promosi?</h2>
                        <p class="text-gray-600 ">Anda yakin ingin menghapus promosi <br>
                            "<strong x-text="deleteForm.title"></strong>"? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="flex justify-center space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="deleteModal = false"
                            class="px-5 py-2.5 w-full bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 w-full bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition">
                            Ya, Hapus
                        </button>
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
                    <h2 class="text-2xl font-semibold text-white">Detail Promosi</h2>
                    <button type="button" @click="detailModal = false"
                        class="text-green-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto" x-show="detailPromo">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <img :src="detailPromo?.image && detailPromo.image.startsWith('http') ?
                                detailPromo.image :
                                (detailPromo?.image ? '{{ asset('') }}' + detailPromo.image :
                                    'https://placehold.co/300x300/e0e0e0/757575?text=N/A')"
                                class="w-full h-auto rounded-lg object-cover shadow-md border border-gray-200">
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-3xl font-bold text-gray-900" x-text="detailPromo?.title"></h3>
                            <p class="text-lg font-semibold text-green-600 mt-2"
                                x-text="formatCurrency(detailPromo?.price)"></p>

                            <div class="flex items-center space-x-2 mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': detailPromo?.is_active,
                                        'bg-yellow-100 text-yellow-800': !detailPromo?.is_active
                                    }">
                                    <span x-text="detailPromo?.is_active ? 'Aktif' : 'Non-Aktif'"></span>
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': detailPromo?.status == 'paid',
                                        'bg-yellow-100 text-yellow-800': detailPromo?.status == 'pending',
                                        'bg-gray-100 text-gray-800': detailPromo?.status == 'expired'
                                    }"
                                    x-text="detailPromo?.status.charAt(0).toUpperCase() + detailPromo?.status.slice(1)">
                                </span>
                            </div>

                            <dl class="mt-6 space-y-4">
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Seller</dt>
                                    <dd class="w-2/3 text-sm text-gray-900" x-text="detailPromo?.seller?.name || 'N/A'">
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Link Tautan</dt>
                                    <dd class="w-2/3 text-sm text-green-700 hover:underline truncate">
                                        <a :href="detailPromo?.link" target="_blank"
                                            x-text="detailPromo?.link || 'Tidak ada link'"></a>
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Tanggal Mulai</dt>
                                    <dd class="w-2/3 text-sm text-gray-900" x-text="formatDate(detailPromo?.start_date)">
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm font-medium text-gray-500">Tanggal Selesai</dt>
                                    <dd class="w-2/3 text-sm text-gray-900" x-text="formatDate(detailPromo?.end_date)">
                                    </dd>
                                </div>
                            </dl>
                        </div>
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

        <div x-show="statusModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="statusModal" @click="statusModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>
            <div x-show="statusModal" @click.away="statusModal = false"
                class="relative w-full max-w-md bg-white rounded-xl shadow-2xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">
                <form :action="statusForm.action" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="status" value="paid">

                    <div class="flex flex-col items-center justify-center p-6 bg-blue-600 rounded-t-xl">
                        <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20">
                            <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0l.879-.659M12 2.25A.75.75 0 0112.75 3v.342a.75.75 0 01-.67.745A4.5 4.5 0 008.25 8.25v.03a.75.75 0 01-1.5 0v-.03a6 6 0 015.9-5.965A.75.75 0 0112.75 3V2.25z" />
                            </svg>
                        </span>
                    </div>
                    <div class="p-6 text-center">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Konfirmasi Pembayaran</h2>
                        <p class="text-gray-600 ">
                            Ubah status promosi <br>
                            "<strong x-text="statusForm.title"></strong>" <br>
                            menjadi <strong class="text-green-600">PAID (Telah Dibayar)</strong>?
                        </p>
                    </div>
                    <div class="flex justify-center space-x-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="button" @click="statusModal = false"
                            class="px-5 py-2.5 w-full bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 w-full bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
                            Ya, Ubah ke Paid
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
