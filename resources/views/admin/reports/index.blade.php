@extends('layouts.admin')
@section('title', 'Laporan Pengguna')
@section('content')

    <div x-data="{
        detailModal: false,
        modalReport: null,
        searchQuery: '{{ request('search', '') }}',
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        },
        getImageUrl(path) {
            if (!path) return 'https://placehold.co/100x100/e0e0e0/757575?text=No+Image';
            if (path.startsWith('http')) return path;
            return '{{ asset('') }}' + path.replace(/^\//, '');
        }
    }" x-init="$nextTick(() => {
        if ($refs.targetSelect) new TomSelect($refs.targetSelect, { create: false, onChange: () => $refs.filterForm.submit() });
        if ($refs.statusSelect) new TomSelect($refs.statusSelect, { create: false, onChange: () => $refs.filterForm.submit() });
    });">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800">Laporan Pengguna</h1>
                <p class="text-gray-500 mt-1">Tinjau laporan penyalahgunaan dari pengguna.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form action="{{ route('admin.reports.index') }}" method="GET" x-ref="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pelapor</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>

                            <input type="text" name="search" placeholder="Nama pelapor..." x-model="searchQuery"
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Objek Terlapor</label>
                        <select name="target_type" x-ref="targetSelect">
                            <option value="">Semua Tipe</option>
                            <option value="buyer" @selected(request('target_type') == 'buyer')>Pembeli (Buyer)</option>
                            <option value="seller" @selected(request('target_type') == 'seller')>Toko (Seller)</option>
                            <option value="product" @selected(request('target_type') == 'product')>Produk</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" x-ref="statusSelect">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') == 'pending')>Menunggu</option>
                            <option value="resolved" @selected(request('status') == 'resolved')>Selesai</option>
                            <option value="rejected" @selected(request('status') == 'rejected')>Ditolak</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <a href="{{ route('admin.reports.index') }}"
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

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pelapor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Terlapor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Alasan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reports as $report)
                            @php
                                $reasonDisplay = $report->reason;
                                $targetType = '-';
                                $targetName = 'Terhapus';
                                $targetImage = null;
                                $targetLink = '#';

                                if ($report->target_type === 'App\Models\User') {
                                    $targetType = 'Buyer';
                                    $targetName = $report->target->name ?? 'User Terhapus';
                                    $targetImage = $report->target->avatar ?? null;
                                    $targetLink = route('admin.buyers.index', ['search' => $targetName]);
                                } elseif ($report->target_type === 'App\Models\Seller') {
                                    $targetType = 'Toko';
                                    $targetName = $report->target->name ?? 'Toko Terhapus';
                                    $targetImage = $report->target->logo ?? null;
                                    $targetLink = route('admin.sellers.index', ['search' => $targetName]);
                                } elseif ($report->target_type === 'App\Models\Product') {
                                    $targetType = 'Produk';
                                    $targetName = $report->target->name ?? 'Produk Terhapus';
                                    $targetImage = $report->target->image ?? null;
                                    $targetLink = route('admin.products.index', ['search' => $targetName]);
                                }

                                $reportData = [
                                    'id' => $report->id,
                                    'status' => $report->status,
                                    'reason' => $reasonDisplay,
                                    'description' => $report->description,
                                    'user_name' => $report->user->name ?? 'User Terhapus',
                                    'user_email' => $report->user->email ?? '-',
                                    'target_type' => $targetType,
                                    'target_name' => $targetName,
                                    'target_image' => $targetImage,
                                    'target_link' => $targetLink,
                                ];
                            @endphp

                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $report->created_at->format('d M Y') }}
                                </td>

                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-900 text-sm">
                                        {{ $report->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-xs text-gray-500">{{ $report->user->email ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 flex justify-center shrink-0">
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wide w-full text-center
                                        {{ $targetType == 'Produk'
                                            ? 'bg-blue-50 text-blue-700 border-blue-100'
                                            : ($targetType == 'Toko'
                                                ? 'bg-purple-50 text-purple-700 border-purple-100'
                                                : 'bg-orange-50 text-orange-700 border-orange-100') }}">
                                                {{ $targetType }}
                                            </span>
                                        </div>

                                        <a href="{{ $targetLink }}"
                                            class="font-bold text-gray-800 text-sm truncate max-w-[150px] hover:text-green-600 hover:underline"
                                            title="{{ $targetName }}">
                                            {{ $targetName }}
                                        </a>
                                    </div>
                                </td>

                                <td class="px-4 py-4 max-w-xs">
                                    <div class="font-bold text-red-600 text-sm">{{ $reasonDisplay }}</div>
                                    <p class="text-gray-500 text-xs mt-1 line-clamp-1 italic">"{{ $report->description }}"
                                    </p>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @if ($report->status == 'pending')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                            Menunggu
                                        </span>
                                    @elseif($report->status == 'resolved')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right">
                                    @if ($report->status == 'pending')
                                        <button @click="detailModal = true; modalReport = {{ json_encode($reportData) }};"
                                            class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition shadow-sm flex items-center justify-end gap-1.5 ml-auto">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Tinjau
                                        </button>
                                    @else
                                        <button @click="detailModal = true; modalReport = {{ json_encode($reportData) }};"
                                            class="bg-white border border-gray-300 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 transition shadow-sm flex items-center justify-end gap-1.5 ml-auto">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        @if (request('search') || request('reason') || request('status'))
                                            <div class="bg-gray-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Laporan Tidak Ditemukan</h3>
                                            <p class="text-sm text-gray-500 max-w-xs mt-1">Tidak ada laporan yang cocok
                                                dengan filter pencarian.</p>
                                            <a href="{{ route('admin.reports.index') }}"
                                                class="mt-3 text-sm font-medium text-blue-600 hover:underline">Reset
                                                Filter</a>
                                        @else
                                            <div class="bg-green-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Semua Aman!</h3>
                                            <p class="text-sm text-gray-500 max-w-xs mt-1">Belum ada laporan pelanggaran
                                                yang masuk.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $reports->links() }}</div>
        </div>

        <div x-show="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;"
            x-cloak>
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="detailModal"
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="px-6 py-4 flex justify-between items-center text-white"
                    :class="modalReport?.status == 'pending' ? 'bg-blue-600' : (modalReport?.status == 'resolved' ?
                        'bg-green-600' : 'bg-red-600')">

                    <h3 class="text-lg font-bold"
                        x-text="modalReport?.status == 'pending' ? 'Tinjau Laporan' : 'Detail Laporan'"></h3>

                    <button @click="detailModal = false" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase font-bold">Pelapor</p>
                            <p class="font-medium text-gray-800 mt-1" x-text="modalReport?.user_name"></p>
                            <p class="text-xs text-gray-500 truncate" x-text="modalReport?.user_email"></p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                            <p class="text-xs text-red-400 uppercase font-bold">Masalah</p>
                            <p class="font-bold text-red-700 mt-1" x-text="modalReport?.reason"></p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Objek Terlapor</p>
                        <div
                            class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-300 transition group">
                            <img :src="getImageUrl(modalReport?.target_image)"
                                class="w-12 h-12 rounded-md object-cover border border-gray-100 shadow-sm mr-3 bg-gray-100">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center mb-0.5">
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 rounded mr-2 uppercase tracking-wide text-white"
                                        :class="modalReport?.target_type == 'Produk' ? 'bg-blue-500' : (modalReport
                                            ?.target_type == 'Toko' ? 'bg-purple-500' : 'bg-orange-500')"
                                        x-text="modalReport?.target_type">
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-gray-800 truncate" x-text="modalReport?.target_name"></p>
                            </div>
                            <a :href="modalReport?.target_link"
                                class="ml-3 p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition"
                                title="Lihat Target">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wide">Keterangan Pelapor</p>
                        <div
                            class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-700 italic leading-relaxed">
                            "<span x-text="modalReport?.description"></span>"
                        </div>
                    </div>
                </div>

                <div x-show="modalReport?.status == 'pending'" class="bg-gray-50 px-6 py-5 border-t border-gray-200">
                    <form :action="'{{ url('admin/reports') }}/' + modalReport?.id" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Catatan Admin
                                (Wajib)</label>
                            <textarea name="admin_note" rows="2" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm placeholder-gray-400 transition"
                                placeholder="Tulis tindakan yang diambil atau alasan penolakan..."></textarea>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="submit" name="status" value="rejected"
                                class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition shadow-sm text-sm">
                                Tolak Laporan
                            </button>
                            <button type="submit" name="status" value="resolved"
                                class="px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md transition text-sm flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Selesaikan Kasus
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="modalReport?.status != 'pending'"
                    class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wide shadow-sm"
                        :class="modalReport?.status == 'resolved' ? 'bg-green-100 text-green-700 border border-green-200' :
                            'bg-red-100 text-red-700 border border-red-200'">
                        <span x-text="modalReport?.status == 'resolved' ? 'Kasus Selesai' : 'Laporan Ditolak'"></span>
                    </span>
                </div>

            </div>
        </div>
    </div>
@endsection
