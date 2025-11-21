@extends('layouts.admin')
@section('title', 'Keuangan & Penarikan')

@section('content')

<div x-data="{
    detailModal: false,
    confirmModal: false, // Modal Konfirmasi (Lapis 2)
    
    modalData: null,
    searchQuery: '{{ request('search', '') }}',
    rejectReason: '',
    
    confirmType: '', // 'approve' atau 'reject'

    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    },
    formatDate(dateString) {
        if(!dateString) return '-';
        return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    },

    // Fungsi untuk submit form yang sesuai setelah konfirmasi
    submitAction() {
        if (this.confirmType === 'approve') {
            this.$refs.approveForm.submit();
        } else if (this.confirmType === 'reject') {
            this.$refs.rejectForm.submit();
        }
    }
}"
x-init="$nextTick(() => {
    new TomSelect($refs.statusSelect, { create: false, controlInput: null, placeholder: 'Semua Status', onChange: () => $refs.filterForm.submit() });
});">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800">Pencairan Dana</h1>
            <p class="text-gray-500 mt-1">Kelola permintaan penarikan saldo dari seller.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-yellow-100 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Pending (Wajib Bayar)</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1" x-text="formatCurrency({{ $totalPending }})"></h2>
            </div>
            <div class="p-3 bg-yellow-50 rounded-full text-yellow-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-green-100 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Berhasil Dicairkan</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1" x-text="formatCurrency({{ $totalPaid }})"></h2>
            </div>
            <div class="p-3 bg-green-50 rounded-full text-green-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
        <form action="{{ route('admin.withdrawals.index') }}" method="GET" x-ref="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Seller / Rekening</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" name="search" placeholder="Nama seller, bank, atau pemilik rekening..." x-model="searchQuery" @keydown.enter.prevent="$refs.filterForm.submit()" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" x-ref="statusSelect">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status')=='pending')>Menunggu Transfer</option>
                        <option value="approved" @selected(request('status')=='approved')>Berhasil (Approved)</option>
                        <option value="rejected" @selected(request('status')=='rejected')>Ditolak</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Request</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Seller</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bank Tujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($withdrawals as $wd)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $wd->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $wd->seller->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            <div class="font-bold text-gray-800">{{ $wd->bank_name }}</div>
                            <div class="text-xs text-gray-500">{{ $wd->account_number }}</div>
                            <div class="text-xs text-gray-500 uppercase">{{ $wd->account_holder }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm font-bold text-green-600">
                            Rp {{ number_format($wd->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                {{ $wd->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $wd->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $wd->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($wd->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button @click="detailModal = true; modalData = {{ $wd->toJson() }}; modalData.seller_name = '{{ $wd->seller->name ?? '' }}';" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition" title="Proses">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-12 text-gray-400">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm">Belum ada permintaan penarikan.</span>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $withdrawals->links() }}</div>
    </div>

    <div x-show="detailModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
        <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        
        <div x-show="detailModal" class="relative w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            
            <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-lg font-bold">Proses Penarikan</h3>
                <button @click="detailModal = false" class="text-green-100 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <div class="p-6 space-y-5">
                
                <div class="text-center">
                    <p class="text-sm text-gray-500 uppercase tracking-wider">Jumlah Penarikan</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-1" x-text="formatCurrency(modalData?.amount)"></h2>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border">
                        <span x-text="modalData?.seller_name"></span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-xs font-bold text-blue-400 uppercase">Tujuan Transfer</p>
                        <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Nama Bank</p>
                            <p class="font-bold text-gray-800 text-lg" x-text="modalData?.bank_name"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">No. Rekening</p>
                            <p class="font-mono font-bold text-gray-800 text-lg tracking-wide select-all" x-text="modalData?.account_number"></p>
                        </div>
                        <div class="col-span-2 border-t border-blue-200 pt-2">
                            <p class="text-gray-500 text-xs">Atas Nama</p>
                            <p class="font-medium text-gray-800 uppercase" x-text="modalData?.account_holder"></p>
                        </div>
                    </div>
                </div>

                <form :action="'{{ url('admin/withdrawals') }}/' + modalData?.id" method="POST" x-ref="approveForm" class="hidden">
                    @csrf @method('PUT')
                    <input type="hidden" name="action" value="approve">
                </form>
                
                <form :action="'{{ url('admin/withdrawals') }}/' + modalData?.id" method="POST" x-ref="rejectForm" class="hidden">
                    @csrf @method('PUT')
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="admin_note" :value="rejectReason">
                </form>

                <div x-show="modalData?.status == 'pending'">
                     <div class="mt-2">
                        <label class="text-xs font-bold text-gray-500">Catatan (Wajib jika menolak)</label>
                        <textarea x-model="rejectReason" class="w-full mt-1 p-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none resize-none" rows="2" placeholder="Alasan penolakan..."></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200" x-show="modalData?.status == 'pending'">
                <div class="grid grid-cols-2 gap-3">
                    <button @click="confirmType = 'reject'; confirmModal = true" 
                            :disabled="!rejectReason"
                            class="w-full py-2.5 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Tolak
                    </button>
                    
                    <button @click="confirmType = 'approve'; confirmModal = true" 
                            class="w-full py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold transition shadow-md">
                        Sudah Ditransfer
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center" x-show="modalData?.status != 'pending'">
                <p class="text-sm text-gray-500">Transaksi ini telah diproses pada <span class="font-bold" x-text="formatDate(modalData?.updated_at)"></span></p>
                <p class="text-xs text-gray-400 mt-1" x-show="modalData?.admin_note">Catatan: <span x-text="modalData?.admin_note"></span></p>
            </div>
        </div>
    </div>

    <div x-show="confirmModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="display: none;">
        <div x-show="confirmModal" @click="confirmModal = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        
        <div x-show="confirmModal" class="relative w-full max-w-sm bg-white rounded-xl shadow-2xl overflow-hidden" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4"
                     :class="confirmType === 'approve' ? 'bg-green-100' : 'bg-red-100'">
                    
                    <svg x-show="confirmType === 'approve'" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    
                    <svg x-show="confirmType === 'reject'" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-gray-900" x-text="confirmType === 'approve' ? 'Konfirmasi Transfer' : 'Konfirmasi Penolakan'"></h3>
                
                <p class="text-sm text-gray-500 mt-2" x-show="confirmType === 'approve'">
                    Apakah Anda yakin <strong>sudah melakukan transfer</strong> uang sejumlah tersebut ke rekening seller? Aksi ini tidak dapat dibatalkan.
                </p>
                <p class="text-sm text-gray-500 mt-2" x-show="confirmType === 'reject'">
                    Dana akan <strong>dikembalikan</strong> ke saldo akun seller. Lanjutkan penolakan?
                </p>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-2">
                <button @click="submitAction()" type="button" 
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:text-sm transition"
                        :class="confirmType === 'approve' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'">
                    Ya, Lanjutkan
                </button>
                <button @click="confirmModal = false" type="button" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

</div>
@endsection