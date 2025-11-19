@extends('layouts.admin')
@section('title', 'Laporan Pengguna')
@section('content')

    <div x-data="{
        detailModal: false,
        modalReport: null,
        searchQuery: '{{ request('search', '') }}',
    
        getImageUrl(path) {
            if (!path) return 'https://placehold.co/100x100/e0e0e0/757575?text=No+Image';
            if (path.startsWith('http')) return path;
            return '{{ asset('') }}' + path;
        }
    }" x-init="$nextTick(() => {
        new TomSelect($refs.reasonSelect, { create: false, onChange: () => $refs.filterForm.submit() });
        new TomSelect($refs.statusSelect, { create: false, onChange: () => $refs.filterForm.submit() });
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                        <select name="reason" x-ref="reasonSelect">
                            <option value="">Semua Alasan</option>
                            <option value="fake" @selected(request('reason') == 'fake')>Barang Palsu</option>
                            <option value="mismatch" @selected(request('reason') == 'mismatch')>Tidak Sesuai</option>
                            <option value="scam" @selected(request('reason') == 'scam')>Penipuan</option>
                            <option value="others" @selected(request('reason') == 'others')>Lainnya</option>
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
                            class="w-full h-10 px-5 py-5.5 flex items-center justify-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Reset
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reports as $report)
                            @php
                                $targetName = $report->product
                                    ? $report->product->name
                                    : ($report->seller
                                        ? $report->seller->name
                                        : 'Unknown');
                                $targetType = $report->product ? 'Produk' : 'Toko';
                                $targetImage = $report->product
                                    ? $report->product->image
                                    : ($report->seller
                                        ? $report->seller->logo
                                        : null);

                                $targetLink = '#';
                                if ($report->product) {
                                    $targetLink = route('admin.products.index', ['search' => $report->product->name]);
                                } elseif ($report->seller) {
                                    $targetLink = route('admin.sellers.index', ['search' => $report->seller->name]);
                                }

                                $reportData = [
                                    'id' => $report->id,
                                    'status' => $report->status,
                                    'reason' => ucfirst($report->reason),
                                    'description' => $report->description,
                                    'created_at_formatted' => $report->created_at->format('d M Y'),
                                    'user_name' => $report->user->name ?? 'User Terhapus',
                                    'user_email' => $report->user->email ?? '-',
                                    'target_type' => $targetType,
                                    'target_name' => $targetName,
                                    'target_image' => $targetImage,
                                    'target_link' => $targetLink,
                                ];
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $report->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                    {{ $report->user->name ?? 'User Terhapus' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    @if ($report->product)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                            Produk
                                        </span>
                                        <span
                                            class="truncate w-40 inline-block align-middle">{{ $report->product->name }}</span>
                                    @elseif($report->seller)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                                            Toko
                                        </span>
                                        {{ $report->seller->name }}
                                    @else
                                        <span class="text-gray-400 italic">Terhapus</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ ucfirst($report->reason) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $report->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $report->status == 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $report->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <button
                                        @click="
                                detailModal = true; 
                                modalReport = {{ json_encode($reportData) }};
                            "
                                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Tinjau
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        @if (request('search') || request('reason') || request('status'))
                                            <div class="bg-gray-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Laporan Tidak Ditemukan</h3>
                                            <p class="text-sm text-gray-500 max-w-xs mt-1">
                                                Tidak ada laporan yang cocok dengan filter atau kata kunci pencarian Anda.
                                            </p>
                                        @else
                                            <div class="bg-green-50 rounded-full p-4 mb-4">
                                                <svg class="w-12 h-12 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Semua Aman Terkendali!</h3>
                                            <p class="text-sm text-gray-500 max-w-xs mt-1">
                                                Tidak ada laporan masalah atau pelanggaran dari pengguna saat ini.
                                            </p>
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

        <div x-show="detailModal" class="fixed inset-0 z-99 flex items-center justify-center p-4" style="display: none;">
            <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div x-show="detailModal" class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Detail Laporan</h3>
                    <button @click="detailModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <div class="p-6 space-y-5">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase font-bold">Pelapor</p>
                            <p class="font-medium text-gray-800 mt-1" x-text="modalReport?.user_name"></p>
                            <p class="text-xs text-gray-500 truncate" x-text="modalReport?.user_email"></p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                            <p class="text-xs text-red-400 uppercase font-bold">Jenis Masalah</p>
                            <p class="font-bold text-red-700 mt-1" x-text="modalReport?.reason"></p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-2 font-medium">Objek Terlapor:</p>
                        <div
                            class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-300 transition group">
                            <img :src="getImageUrl(modalReport?.target_image)"
                                class="w-12 h-12 rounded-md object-cover border mr-3 bg-gray-100">

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded mr-2"
                                        :class="modalReport?.target_type == 'Produk' ? 'bg-blue-100 text-blue-700' :
                                            'bg-purple-100 text-purple-700'"
                                        x-text="modalReport?.target_type">
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-gray-800 truncate mt-0.5"
                                    x-text="modalReport?.target_name"></p>
                            </div>

                            <a :href="modalReport?.target_link"
                                class="ml-3 p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition"
                                title="Lihat/Kelola Target">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-medium">Keterangan Pelapor:</p>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700 italic">
                            <span class="text-gray-400 mr-1">"</span><span x-text="modalReport?.description"></span><span
                                class="text-gray-400 ml-1">"</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3"
                    x-show="modalReport?.status == 'pending'">
                    <form :action="'{{ url('admin/reports') }}/' + modalReport?.id" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-red-600 text-sm font-medium transition">
                            Tolak Laporan
                        </button>
                    </form>

                    <form :action="'{{ url('admin/reports') }}/' + modalReport?.id" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-md transition flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Selesaikan Kasus
                        </button>
                    </form>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center"
                    x-show="modalReport?.status != 'pending'">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        :class="modalReport?.status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        <span
                            x-text="modalReport?.status == 'resolved' ? 'Kasus Telah Diselesaikan' : 'Laporan Ditolak'"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
