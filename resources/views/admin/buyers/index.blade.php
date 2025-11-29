@extends('layouts.admin')
@section('title', 'Data Buyer')
@section('content')

    <div x-data="{
        detailModal: false,
        suspendModal: false,
        deleteModal: false,
        modalBuyer: null,
        actionUrl: '',
        searchQuery: '{{ request('search', '') }}',
    
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).format(date);
        },
    
        getAvatarUrl(path) {
            if (!path) return 'https://placehold.co/100x100/e0e0e0/757575?text=User';
            if (path.startsWith('http')) return path;
            return '{{ asset('storage') }}/' + path;
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.statusSelect, {
            create: false,
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
                <h1 class="text-3xl font-semibold text-gray-800">Data Buyer</h1>
                <p class="text-gray-500 mt-1">Kelola akun pembeli yang terdaftar di Green Mart.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.buyers.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <div class="md:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Buyer</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input type="text" name="search" placeholder="Nama atau email..." x-model="searchQuery"
                                @keydown.enter.prevent="$refs.filterForm.submit()"
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                        <select name="status" x-ref="statusSelect">
                            <option value="">Semua Status</option>

                            <option value="verified" @selected(request('status') == 'verified')>Terverifikasi</option>
                            <option value="unverified" @selected(request('status') == 'unverified')>Belum Verifikasi / Suspend</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tampil</label>
                        <select name="per_page" x-ref="perPageSelect" x-cloak">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>{{ $option }} data
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-1 flex items-end">
                        <a href="{{ route('admin.buyers.index') }}"
                            class="w-full h-[42px] flex items-center justify-center bg-white border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-red-600 transition shadow-sm"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="ml-2 text-sm font-bold">Reset</span>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Bergabung
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($buyers as $index => $buyer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm w-10">
                                    <span
                                        class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 font-medium border border-gray-300 shadow-sm">
                                        {{ $buyers->firstItem() + $index }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    <button @click="detailModal = true; modalBuyer = {{ $buyer->toJson() }};"
                                        class="flex items-center text-left group">
                                        <img :src="getAvatarUrl('{{ $buyer->avatar }}')"
                                            class="w-15 h-15 rounded-full object-cover border-2 border-white shadow-sm mr-3 bg-gray-100">
                                        <div>
                                            <p
                                                class="text-sm  text-gray-900 group-hover:text-green-600 transition font-bold">
                                                {{ $buyer->name }}</p>
                                        </div>
                                    </button>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ $buyer->email }}
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($buyer->created_at)->locale('id')->translatedFormat('d F Y') }}
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    <button
                                        @click="
                                        suspendModal = true;
                                        modalBuyer = {{ $buyer->toJson() }};
                                        actionUrl = '{{ route('admin.buyers.update', $buyer->id) }}';
                                    "
                                        class="group focus:outline-none" title="Klik untuk mengubah status">
                                        @if ($buyer->status == 'active' || $buyer->status == 'active')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 group-hover:bg-green-200 transition">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 group-hover:bg-red-200 transition">
                                                Suspended
                                            </span>
                                        @endif
                                    </button>
                                </td>

                                <td class="px-4 py-4 text-sm font-medium">
                                    <button
                                        @click="
                                        deleteModal = true;
                                        modalBuyer = {{ $buyer->toJson() }};
                                        actionUrl = '{{ route('admin.buyers.destroy', $buyer->id) }}';
                                    "
                                        class="text-red-600 hover:text-red-800 transition" title="Hapus Akun">
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
                                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A1.5 1.5 0 0118 21.75H6.A1.5 1.5 0 014.501 20.118z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Buyer Tidak Ditemukan</h3>
                                            <p class="text-sm text-gray-500 mt-1">Tidak ada pembeli yang cocok dengan
                                                kriteria pencarian Anda.</p>
                                        @else
                                            <div class="bg-indigo-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18 18.72a9.094 9.094 0 00-3.741-.56c-.301.002-.59.027-.872.074a8.991 8.991 0 01-3.132 0c-.282-.047-.571-.072-.872-.074a9.094 9.094 0 00-3.741.56C4.044 19.166 3 20.428 3 21.75v.25c0 .621.504 1.125 1.125 1.125h16.75c.621 0 1.125-.504 1.125-1.125v-.25c0-1.322-1.044-2.584-2.875-3.03zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Belum Ada Buyer</h3>
                                            <p class="text-sm text-gray-500 mt-1">Belum ada pengguna yang mendaftar sebagai
                                                pembeli.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $buyers->links() }}
            </div>
        </div>

        <div x-show="detailModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            <div x-show="detailModal" class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="h-24 bg-green-500 relative">
                    <div class="absolute inset-0 bg-black/10"></div>
                </div>

                <div class="px-8 pb-8">
                    <div class="flex items-end -mt-6 mb-6 relative z-10">
                        <img :src="getAvatarUrl(modalBuyer?.avatar)"
                            class="w-24 h-24 rounded-full border-4 border-white object-cover bg-white shadow-md">
                        <div class="ml-4 mb-1">
                            <h2 class="text-2xl font-bold text-gray-800" x-text="modalBuyer?.name"></h2>
                            <p class="text-sm text-gray-500" x-text="modalBuyer?.email"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Akun</h4>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Status</p>
                                    <span
                                        class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="(modalBuyer?.status == 'active' || modalBuyer?.status == 'active') ?
                                        'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        x-text="(modalBuyer?.status == 'active' || modalBuyer?.status == 'active') ? 'Aktif' : 'Suspended'">
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Bergabung Sejak</p>
                                    <p class="text-sm font-medium text-gray-900 mt-1"
                                        x-text="formatDate(modalBuyer?.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button @click="detailModal = false"
                            class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">Tutup</button>
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
                x-transition:leave-end="opacity-0"></div>
            <div x-show="suspendModal" class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <form :action="actionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="toggle_status">

                    <div class="p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900"
                            x-text="(modalBuyer?.status == 'active' || modalBuyer?.status == 'active') ? 'Bekukan Akun?' : 'Aktifkan Akun?'">
                        </h3>
                        <div class="mt-4 text-base text-gray-600">
                            Apakah Anda yakin ingin mengubah status akun untuk: <br>
                            <span class="font-bold text-gray-900 text-lg" x-text="modalBuyer?.name"></span>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-xl">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto transition"
                            :class="(modalBuyer?.status == 'active' || modalBuyer?.status == 'active') ?
                            'bg-red-600 hover:bg-red-700 focus:ring-red-500' :
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
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            <div x-show="deleteModal" class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden"
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
                        <h3 class="text-2xl font-bold text-gray-900">Hapus Akun?</h3>
                        <div class="mt-4 text-base text-gray-600">
                            Anda akan menghapus akun buyer: <br>
                            <span class="font-bold text-gray-900 text-lg" x-text="modalBuyer?.name"></span>
                        </div>
                        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4 text-left">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h4 class="font-bold text-red-800 text-sm uppercase tracking-wide">Peringatan Penting
                                    </h4>
                                    <p class="text-sm text-red-700 mt-1">Data user akan dihapus permanen dan tidak dapat
                                        dikembalikan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-xl">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition">Ya,
                            Hapus Permanen</button>
                        <button type="button" @click="deleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
