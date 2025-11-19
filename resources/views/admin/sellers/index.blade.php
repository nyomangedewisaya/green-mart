@extends('layouts.admin')
@section('title', 'Data Seller')
@section('content')

    <div x-data="{
        detailModal: false,
        suspendModal: false,
        deleteModal: false,
        modalSeller: null,
        actionUrl: '',
        searchQuery: '{{ request('search', '') }}',
    
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },
    
        getImageUrl(path, type) {
            if (!path) {
                return type === 'logo' ?
                    'https://placehold.co/100x100/e0e0e0/757575?text=Logo' :
                    'https://placehold.co/600x200/e0e0e0/757575?text=No+Banner';
            }
    
            if (path.startsWith('http')) {
                return path;
            }
    
            return '{{ asset('') }}' + path;
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.statusSelect, {
            create: false,
            placeholder: 'Semua Status',
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
                <h1 class="text-3xl font-semibold text-gray-800">Data Seller</h1>
                <p class="text-gray-500 mt-1">Kelola data toko dan status verifikasi penjual.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.sellers.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Toko / Pemilik</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>

                            <input type="text" name="search" placeholder="Nama toko, pemilik, atau email..."
                                x-model="searchQuery" @keydown.enter.prevent="$refs.filterForm.submit()"
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none transition-all">

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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Verifikasi</label>
                        <select name="status" x-ref="statusSelect" x-cloak>
                            <option value="">Semua Status</option>
                            <option value="verified" @selected(request('status') == 'verified')>Terverifikasi</option>
                            <option value="unverified" @selected(request('status') == 'unverified')>Belum Verifikasi / Suspend</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tampil</label>
                        <select name="per_page" x-ref="perPageSelect" x-cloak>
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>{{ $option }} per
                                    halaman
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <a href="{{ route('admin.sellers.index') }}"
                            class="w-full h-10 px-5 py-5.5 flex items-center justify-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Toko</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pemilik (User)
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kontak</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sellers as $index => $seller)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm w-10">
                                    <span
                                        class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 font-medium border border-gray-300 shadow-sm">
                                        {{ $sellers->firstItem() + $index }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    <button
                                        @click="detailModal = true; modalSeller = {{ $seller->toJson() }}; modalSeller.user = {{ $seller->user->toJson() }};"
                                        class="flex items-center text-left group">
                                        <img src="{{ $seller->logo ? asset($seller->logo) : 'https://placehold.co/100x100/e0e0e0/757575?text=Logo' }}"
                                            alt="{{ $seller->name }}"
                                            class="w-15 h-15 rounded-full object-cover border-2 border-white shadow-sm mr-3 bg-gray-100">
                                        <div>
                                            <p
                                                class="text-sm text-gray-900 group-hover:text-green-600 transition font-bold">
                                                {{ $seller->name }}</p>
                                            <p class="text-xs text-gray-500">@<span>{{ $seller->slug }}</span></p>
                                        </div>
                                    </button>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ $seller->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">{{ $seller->user->email ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div>{{ $seller->phone }}</div>
                                    <div class="text-xs text-gray-400 truncate max-w-[150px]"
                                        title="{{ $seller->address }}">{{ $seller->address }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    <button
                                        @click="
                                        suspendModal = true;
                                        modalSeller = {{ $seller->toJson() }};
                                        actionUrl = '{{ route('admin.sellers.update', $seller) }}';
                                    "
                                        class="group focus:outline-none" title="Klik untuk mengubah status">
                                        @if ($seller->is_verified)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 group-hover:bg-green-200 transition">
                                                Verified
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 group-hover:bg-red-200 transition">
                                                Unverified
                                            </span>
                                        @endif
                                    </button>
                                </td>

                                <td class="px-4 py-4 text-sm font-medium">
                                    <button
                                        @click="
                                        deleteModal = true;
                                        modalSeller = {{ $seller->toJson() }};
                                        actionUrl = '{{ route('admin.sellers.destroy', $seller) }}';
                                    "
                                        class="text-red-600 hover:text-red-800 transition" title="Hapus Permanen">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 001.5.06l.3-7.5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        @if (request('search') || request('status'))
                                            <div class="bg-gray-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Seller Tidak Ditemukan</h3>
                                            <p class="text-sm text-gray-500 mt-1">Coba sesuaikan filter status atau kata
                                                kunci pencarian.</p>
                                        @else
                                            <div class="bg-blue-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.617A3.001 3.001 0 009 9.35M18 21v-7.75V21zM13.5 9.35v2.25m0-2.25a3.001 3.001 0 003.75-.617A3.001 3.001 0 0021 9.35M13.5 9.35v2.25m0-2.25V4.875c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V9.35M3.75 21V9.35M16.5 4.875V9.35m0-4.475L18.75 9.35" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Belum Ada Seller</h3>
                                            <p class="text-sm text-gray-500 mt-1">Belum ada toko yang terdaftar dan aktif
                                                di platform ini.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $sellers->links() }}
            </div>
        </div>

        <div x-show="detailModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            <div x-show="detailModal" class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="h-32 bg-gray-300 relative">
                    <img :src="getImageUrl(modalSeller?.banner, 'banner')" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/20"></div>

                    <button @click="detailModal = false"
                        class="absolute top-4 right-4 bg-white/20 hover:bg-white/40 text-white rounded-full p-1 backdrop-blur-sm transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 pb-6">
                    <div class="flex items-end -mt-6 mb-4 relative z-10">
                        <img :src="getImageUrl(modalSeller?.logo, 'logo')"
                            class="w-24 h-24 rounded-full border-4 border-white object-cover bg-white shadow-md">
                        <div class="ml-4 mb-1">
                            <h2 class="text-2xl font-bold text-gray-800" x-text="modalSeller?.name"></h2>
                            <div class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-500" x-text="'@' + modalSeller?.slug"></span>
                                <span class="text-gray-300">|</span>
                                <span class="flex items-center text-yellow-500 font-medium">
                                    <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span x-text="modalSeller?.rating || '0.0'"></span>
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 text-right mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                :class="modalSeller?.is_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                x-text="modalSeller?.is_verified ? 'Verified' : 'Unverified'">
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Informasi Pemilik</h4>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900" x-text="modalSeller?.user?.name || '-'">
                                    </p>
                                    <p class="text-xs text-gray-500" x-text="modalSeller?.user?.email || '-'"></p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Kontak & Alamat</h4>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span x-text="modalSeller?.phone"></span>
                                </p>
                                <p class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span x-text="modalSeller?.address"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Toko</h4>
                        <p class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100"
                            x-text="modalSeller?.description || 'Tidak ada deskripsi.'"></p>
                    </div>

                    <div class="mt-6 text-xs text-gray-400 text-right">
                        Terdaftar pada: <span x-text="formatDate(modalSeller?.created_at)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="suspendModal" class="fixed inset-0 z-99 flex items-center justify-center p-4"
            style="display: none;">
            <div x-show="suspendModal" @click="suspendModal = false"
                class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <div x-show="suspendModal" @click.away="suspendModal = false"
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <form :action="actionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="toggle_status">

                    <div class="p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900"
                            x-text="modalSeller?.is_verified ? 'Nonaktifkan Verifikasi?' : 'Verifikasi Toko Ini?'"></h3>

                        <div class="mt-4 text-base text-gray-600">
                            Apakah Anda yakin ingin mengubah status untuk toko: <br>
                            <span class="font-bold text-gray-900 text-lg" x-text="modalSeller?.name"></span>
                        </div>

                        <div class="mt-6" x-show="modalSeller?.is_verified">
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-left flex items-start">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-red-800 text-sm">Konsekuensi Suspend:</h4>
                                    <p class="text-sm text-red-700 mt-1">
                                        Toko akan ditandai sebagai <strong>Unverified</strong>. Produk mereka akan
                                        disembunyikan dari hasil pencarian publik.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6" x-show="!modalSeller?.is_verified">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-left flex items-start">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-green-800 text-sm">Verifikasi Berhasil:</h4>
                                    <p class="text-sm text-green-700 mt-1">
                                        Toko akan menjadi <strong>Verified</strong> dan produk mereka dapat dilihat oleh
                                        pembeli.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto transition"
                            :class="modalSeller?.is_verified ? 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500' :
                                'bg-green-600 hover:bg-green-700 focus:ring-green-500'">
                            Ya, Lanjutkan
                        </button>
                        <button type="button" @click="suspendModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
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
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <form :action="actionUrl" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900">Hapus Toko Permanen?</h3>

                        <div class="mt-4 text-base text-gray-600">
                            Anda akan menghapus toko: <br>
                            <span class="font-bold text-gray-900 text-lg" x-text="modalSeller?.name"></span>
                        </div>

                        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4 text-left">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h4 class="font-bold text-red-800 text-sm uppercase tracking-wide">Peringatan Penting
                                    </h4>
                                    <ul class="mt-1 list-disc list-inside text-sm text-red-700 space-y-1">
                                        <li>Toko dan seluruh produknya akan dihapus.</li>
                                        <li><strong>Akun User (Login)</strong> pemilik TIDAK akan dihapus.</li>
                                        <li>Tindakan ini <strong>tidak dapat dibatalkan</strong>.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition">
                            Ya, Hapus Permanen
                        </button>
                        <button type="button" @click="deleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
