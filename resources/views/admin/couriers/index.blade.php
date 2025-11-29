@extends('layouts.admin')
@section('title', 'Master Data Kurir')

@section('content')

    <div x-data="{
        createModal: false,
        editModal: false,
        editForm: { id: null, name: '', service: '', cost: 0, estimation: '' },
        editAction: '',
        searchQuery: '{{ request('search', '') }}',
    
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        },
    
        openCreateModal() {
            this.createModal = true;
            this.$nextTick(() => { if (this.$refs.formCreate) this.$refs.formCreate.reset(); });
        },
    
        openEditModal(courier, url) {
            this.editModal = true;
            this.editForm = courier;
            this.editAction = url;
        }
    
    }" x-init="$nextTick(() => {
        // Inisialisasi TomSelect untuk Filter Status
        if ($refs.filterStatusSelect) {
            new TomSelect($refs.filterStatusSelect, {
                create: false,
                placeholder: 'Semua Status',
                onChange: () => $refs.filterForm.submit()
            });
        }
    })" class="space-y-8 font-inter">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Logistik & Kurir</h1>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed max-w-md">Kelola mitra pengiriman, atur biaya ongkir
                    flat, dan pantau kurir yang paling sering digunakan pelanggan.</p>
            </div>
            <button @click="openCreateModal()"
                class="mt-4 sm:mt-0 flex items-center justify-center px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-md hover:bg-green-700 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Kurir
            </button>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100/80 flex items-center gap-5 hover:shadow-md transition group">
                <div
                    class="p-4 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-600 group-hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Total Opsi</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalCouriers }} <span
                            class="text-sm font-medium text-gray-500">Layanan</span></h3>
                </div>
            </div>
            <div
                class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100/80 flex items-center gap-5 hover:shadow-md transition group">
                <div
                    class="p-4 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-600 group-hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Status Aktif</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $activeCouriers }} <span
                            class="text-sm font-medium text-gray-500">Siap Pakai</span></h3>
                </div>
            </div>
            <div
                class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100/80 flex items-center gap-5 hover:shadow-md transition group">
                <div
                    class="p-4 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-600 group-hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Rata-rata Ongkir</p>
                    <h3 class="text-3xl font-black text-gray-800">
                        {{ number_format($avgCost / 1000, 0) }}k <span class="text-sm font-medium text-gray-500">/
                            Flat</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- LEFT SIDE: TABLE & FILTER (3/5 Width) --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Filter Bar --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <form action="{{ route('admin.couriers.index') }}" method="GET" x-ref="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                            <div class="md:col-span-7">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari
                                    Kurir</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>

                                    <input type="text" id="search" name="search" placeholder="Ketik nama kurir..."
                                        x-model="searchQuery" @keydown.enter.prevent="$refs.filterForm.submit()"
                                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:outline-none transition-all">

                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <button :type="searchQuery ? 'button' : 'submit'"
                                            @click="if (searchQuery) { searchQuery = ''; $nextTick(() => $refs.filterForm.submit()); }"
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

                            <div class="md:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" x-ref="filterStatusSelect" class="hidden">
                                    <option value="">Semua Status</option>
                                    <option value="active" @selected(request('status') == 'active')>Aktif</option>
                                    <option value="inactive" @selected(request('status') == 'inactive')>Nonaktif</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 flex items-end">
                                <a href="{{ route('admin.couriers.index') }}"
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

                {{-- Table --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="bg-gray-50/50 text-gray-500 uppercase font-bold text-xs tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Kurir & Layanan</th>
                                    <th class="px-6 py-4">Estimasi Waktu</th>
                                    <th class="px-6 py-4">Biaya Flat</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($couriers as $courier)
                                    <tr class="hover:bg-green-50/40 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-gray-100 text-gray-500 rounded-lg">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 text-base">{{ $courier->name }}</p>
                                                    <span
                                                        class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wide border border-green-100/50">
                                                        {{ $courier->service }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $courier->estimation }}</td>
                                        <td class="px-6 py-4 font-mono font-bold text-green-700 text-base">
                                            Rp {{ number_format($courier->cost, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <form action="{{ route('admin.couriers.update', $courier->slug) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="toggle_active" value="1">
                                                <button type="submit"
                                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                                    :class="{{ $courier->is_active }} ? 'bg-green-500' : 'bg-gray-200'"
                                                    title="Klik untuk mengubah status">
                                                    <span class="sr-only">Toggle Status</span>
                                                    <span
                                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"
                                                        :class="{{ $courier->is_active }} ? 'translate-x-6' : 'translate-x-1'">
                                                    </span>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button
                                                @click="openEditModal({{ $courier->toJson() }}, '{{ route('admin.couriers.update', $courier->slug) }}')"
                                                class="text-blue-600 hover:text-blue-800 transition transform hover:-translate-y-0.5"
                                                title="Edit Kurir">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                <p class="font-medium">Belum ada data kurir yang tersedia.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                        {{ $couriers->links() }}
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: CHART (2/5 Width) --}}
            <div class="lg:col-span-2">
                <div
                    class="bg-white p-6 rounded-2xl shadow-xs border border-gray-200/80 sticky top-24 overflow-hidden relative">
                    {{-- Background Accent --}}
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-green-50/50 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none">
                    </div>

                    <div class="mb-8 relative z-10">
                        <h3 class="text-xl font-bold text-gray-900">Kurir Terpopuler</h3>
                        <p class="text-sm text-gray-500 mt-1">Berdasarkan frekuensi penggunaan dalam pesanan.</p>
                    </div>

                    {{-- MODERN HORIZONTAL BAR CHART --}}
                    <div class="relative h-80 w-full z-10">
                        <canvas id="courierChart"></canvas>
                    </div>

                    @if (empty($chartLabels) || $chartLabels[0] == 'Belum Ada Data')
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-white/80 z-20 backdrop-blur-sm rounded-2xl">
                            <p class="text-gray-500 font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Menunggu data transaksi masuk...
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div x-show="createModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="createModal" @click="createModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>
            <div x-show="createModal" @click.away="createModal = false"
                class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">

                <div class="flex items-center justify-between p-6 bg-green-600 rounded-t-xl">
                    <h2 class="text-2xl font-semibold text-white">Tambah Kurir Baru</h2>
                    <button type="button" @click="createModal = false"
                        class="text-green-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.couriers.store') }}" method="POST" x-ref="formCreate"
                    class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Kurir <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white outline-none transition"
                            placeholder="Contoh: JNE, GoSend">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Layanan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="service" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white outline-none transition"
                                placeholder="REG, Instant">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Estimasi <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="estimation" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white outline-none transition"
                                placeholder="1-2 Hari">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Biaya Ongkir (Flat) <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="cost" required
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white outline-none font-bold text-gray-800 transition"
                                placeholder="0">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200/50 transition transform hover:-translate-y-0.5">Simpan
                        Data</button>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT --}}
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
                    <h2 class="text-2xl font-semibold text-white">Edit Kurir</h2>
                    <button type="button" @click="editModal = false" class="text-blue-100 hover:text-white transition">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form :action="editAction" method="POST" class="p-6 space-y-5">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Kurir <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editForm.name" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Layanan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="service" x-model="editForm.service" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Estimasi <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="estimation" x-model="editForm.estimation" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Biaya Ongkir (Flat) <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="cost" x-model="editForm.cost" required
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none font-bold text-gray-800 transition">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200/50 transition transform hover:-translate-y-0.5">Simpan
                        Perubahan</button>
                </form>
            </div>
        </div>

    </div>

    {{-- SCRIPT CHART (MODERN HORIZONTAL BAR) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('courierChart');
            if (ctx) {
                // Setup Gradient Hijau untuk Bar Chart
                const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 300, 0);
                gradient.addColorStop(0, 'rgba(22, 163, 74, 0.8)'); // Green-600
                gradient.addColorStop(1, 'rgba(34, 197, 94, 0.4)'); // Green-400

                new Chart(ctx, {
                    type: 'bar', // Gunakan tipe 'bar'
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Jumlah Pesanan',
                            data: @json($chartData),
                            backgroundColor: gradient, // Pakai gradient
                            borderColor: '#16a34a',
                            borderWidth: 1,
                            borderRadius: 8, // Sudut tumpul pada bar
                            barThickness: 25, // Ketebalan bar
                        }]
                    },
                    options: {
                        indexAxis: 'y', // KUNCI: Mengubah jadi Horizontal Bar
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }, // Sembunyikan legend bawaan
                            tooltip: {
                                backgroundColor: '#1f2937',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.x + ' Pesanan Dikirim';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false, // Sembunyikan grid vertikal
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#9ca3af'
                                }
                            },
                            y: {
                                grid: {
                                    color: '#f3f4f6', // Grid horizontal halus
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    color: '#374151'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
