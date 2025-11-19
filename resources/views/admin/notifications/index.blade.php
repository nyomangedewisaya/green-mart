@extends('layouts.admin')
@section('title', 'Manajemen Notifikasi')
@section('content')

<div x-data
     x-init="$nextTick(() => {
        new TomSelect($refs.targetSelect, {
            create: false,
            placeholder: 'Pilih Target Penerima'
        });
     });">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800">Manajemen Notifikasi</h1>
            <p class="text-gray-500 mt-1">Kirim pengumuman massal (Broadcast) kepada pengguna.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5 flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    Buat Pengumuman Baru
                </h2>
                
                <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Target Penerima</label>
                        <div class="relative">
                            <select name="target" x-ref="targetSelect" required>
                                <option value="">Pilih Target Penerima</option>
                                <option value="all">Semua Pengguna (Seller & Buyer)</option>
                                <option value="sellers">Semua Seller</option>
                                <option value="buyers">Semua Buyer</option>
                            </select>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">Notifikasi akan muncul di dashboard pengguna sesuai grup.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-600 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <input type="text" name="title" required 
                                   class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-1 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-all outline-none placeholder-gray-400" 
                                   placeholder="Contoh: Maintenance Sistem">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan</label>
                        <div class="relative group">
                            <div class="absolute top-3 left-0 flex items-start pl-3.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-600 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                </svg>
                            </div>
                            <textarea name="message" rows="5" required 
                                      class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-1 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-all outline-none placeholder-gray-400 resize-none" 
                                      placeholder="Tulis pesan pengumuman di sini..."></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full group relative flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-md hover:-translate-y-0.5">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-green-200 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </span>
                        Kirim Broadcast
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Riwayat Broadcast</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Target Audience</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Isi Pesan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($notifications as $notif)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-40">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900">{{ $notif->created_at->format('d M Y') }}</span>
                                        <span class="text-xs">{{ $notif->created_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium w-48">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $notif->target == 'all' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $notif->target == 'sellers' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $notif->target == 'buyers' ? 'bg-green-100 text-green-800' : '' }}">
                                            
                                            @if($notif->target == 'all') Semua User
                                            @elseif($notif->target == 'sellers') Seller Only
                                            @elseif($notif->target == 'buyers') Buyer Only
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <p class="font-bold text-gray-900 mb-1">{{ $notif->title }}</p>
                                    <p class="text-gray-500 text-xs leading-relaxed">{{ $notif->message }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-green-50 rounded-full p-4 mb-4">
                                            <svg class="w-12 h-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.43.816 1.035.816 1.73 0 .695-.32 1.3-.816 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Belum Ada Pengumuman</h3>
                                        <p class="text-sm text-gray-500 max-w-xs mt-1 text-center">
                                            Anda belum mengirimkan notifikasi sistem atau broadcast kepada pengguna.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>

</div>

@endsection