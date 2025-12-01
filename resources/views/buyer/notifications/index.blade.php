@extends('layouts.buyer')
@section('title', 'Kotak Masuk')

@section('content')

{{-- 1. HEAD KHUSUS (Alpine Collapse Plugin Wajib Ada untuk Smooth Animation) --}}
@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
@endpush

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen font-inter">

    {{-- ALERT SUCCESS (Floating) --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-24 right-4 z-[999] bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3">
            <div class="bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8 animate-fade-up">
        <div class="flex items-center gap-4">
            {{-- TOMBOL KEMBALI KE HOME --}}
            <a href="{{ route('buyer.home') }}" class="p-2.5 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-green-600 hover:border-green-200 hover:shadow-sm transition group" title="Kembali ke Beranda">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>

            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Kotak Masuk</h1>
                <p class="text-sm text-gray-500">Pembaruan pesanan dan info terbaru.</p>
            </div>
        </div>

        @if($notifications->count() > 0)
            <form action="{{ route('buyer.notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="group flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-green-300 hover:bg-green-50 text-gray-600 hover:text-green-700 rounded-lg transition shadow-sm transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-xs font-bold">Baca Semua</span>
                </button>
            </form>
        @endif
    </div>

    {{-- 3. LIST NOTIFIKASI --}}
    <div class="space-y-4">
        @forelse($notifications as $index => $notif)
            @php
                $pivot = $notif->users->first();
                $isRead = $pivot && $pivot->pivot->read_at;

                // Setup Warna & Ikon
                $colors = [
                    'info'    => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-100', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                    'success' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'border' => 'border-green-100', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                    'warning' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                    'danger'  => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'border' => 'border-red-100', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                ];
                $theme = $colors[$notif->type] ?? $colors['info'];
            @endphp

            <div x-data="{ open: false }" 
                 class="group relative rounded-2xl transition-all duration-300 overflow-hidden animate-fade-up
                        {{ $isRead 
                            ? 'bg-gray-50 border border-gray-200 opacity-90' 
                            : 'bg-white border border-gray-100 shadow-md shadow-gray-100 hover:shadow-lg hover:-translate-y-0.5' 
                        }}"
                 style="animation-delay: {{ $index * 75 }}ms">
                
                {{-- Indikator Belum Baca (Garis Kiri) --}}
                @if(!$isRead)
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-green-500 rounded-l-2xl"></div>
                @endif

                {{-- HEADER KARTU (CLICKABLE) --}}
                <div class="p-5 flex gap-4 cursor-pointer select-none" @click="open = !open">
                    
                    {{-- Ikon --}}
                    <div class="shrink-0 pt-1">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $theme['bg'] }} {{ $theme['text'] }} border {{ $theme['border'] }} shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $theme['icon'] !!}
                            </svg>
                        </div>
                    </div>

                    {{-- Konten Utama --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                {{ $notif->title }}
                                @if(!$isRead)
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                @endif
                            </h3>
                            
                            {{-- Waktu & Chevron --}}
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" 
                                     :class="open ? 'rotate-180 text-green-600' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Preview Pesan (Muncul saat tertutup) --}}
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-1 transition-opacity duration-200" 
                           x-show="!open">
                            {{ $notif->message }}
                        </p>

                        {{-- EXPANDED CONTENT (ACCORDION) --}}
                        <div x-show="open" x-collapse>
                            <div class="pt-3 mt-2 border-t border-dashed border-gray-200">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $notif->message }}
                                </p>

                                {{-- Tombol Aksi --}}
                                <div class="flex items-center justify-end gap-3 mt-4">
                                    @if(!$isRead)
                                        <form action="{{ route('buyer.notifications.read', $notif->id) }}" method="POST">
                                            @csrf
                                            {{-- @click.stop untuk mencegah accordion menutup saat tombol diklik --}}
                                            <button @click.stop type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-green-600 bg-green-50 hover:bg-green-100 border border-green-100 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('buyer.notifications.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?');">
                                        @csrf @method('DELETE')
                                        <button @click.stop type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-gray-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- END EXPANDED --}}

                    </div>
                </div>
            </div>
        @empty
            {{-- EMPTY STATE --}}
            <div class="flex flex-col items-center justify-center py-24 text-center animate-fade-up">
                <div class="bg-gray-50 p-6 rounded-full mb-4 border border-gray-100 shadow-inner">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Kotak Masuk Kosong</h3>
                <p class="text-sm text-gray-500 mt-1 max-w-xs">Belum ada notifikasi baru untuk Anda saat ini.</p>
                <a href="{{ route('buyer.home') }}" class="mt-6 px-5 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    Mulai Belanja
                </a>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-8">
        {{ $notifications->links() }}
    </div>

</div>

<style>
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
</style>

@endsection