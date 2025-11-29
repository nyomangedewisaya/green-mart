<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Toko - {{ Auth::user()->seller->name ?? 'Seller' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            display: none;
        }

        html {
            scrollbar-width: none;
        }

        body {
            -ms-overflow-style: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-card {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-700">

    <div
        class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/80 supports-backdrop-filter:bg-white/60">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('seller.dashboard') }}"
                    class="group relative p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all duration-300"
                    title="Kembali ke Dashboard">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pusat Notifikasi</h1>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <p class="text-xs font-medium text-slate-500">Update Realtime</p>
                    </div>
                </div>
            </div>

            @if ($notifications->count() > 0)
                <form action="{{ route('seller.notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="hidden sm:flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-300 text-slate-600 hover:text-indigo-600 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-bold tracking-wide">Tandai Semua Dibaca</span>
                    </button>
                    <button type="submit"
                        class="sm:hidden p-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 pb-20">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="mb-6 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg shadow-emerald-200/50">
                <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($notifications as $index => $notif)
                @php
                    $pivot = $notif->users->first();
                    $isRead = $pivot && $pivot->pivot->read_at;
                    $colors = [
                        'info' => [
                            'bg' => 'bg-indigo-50',
                            'text' => 'text-indigo-600',
                            'border' => 'border-indigo-100',
                        ],
                        'success' => [
                            'bg' => 'bg-emerald-50',
                            'text' => 'text-emerald-600',
                            'border' => 'border-emerald-100',
                        ],
                        'warning' => [
                            'bg' => 'bg-amber-50',
                            'text' => 'text-amber-600',
                            'border' => 'border-amber-100',
                        ],
                        'danger' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100'],
                    ];
                    $theme = $colors[$notif->type] ?? $colors['info'];
                @endphp

                <div x-data="{ open: false }" style="animation-delay: {{ $index * 100 }}ms"
                    class="animate-card group relative rounded-2xl transition-all duration-300 overflow-hidden
                            {{ $isRead
                                ? 'bg-slate-50 border border-slate-200 opacity-80 hover:opacity-100 hover:bg-white hover:shadow-sm'
                                : 'bg-white border border-slate-100 shadow-lg shadow-slate-200/50 ring-1 ring-slate-100 hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-0.5' }}">

                    @if (!$isRead)
                        <div
                            class="absolute left-0 top-0 bottom-0 w-1.5 bg-linear-to-b from-indigo-500 to-purple-500 rounded-l-2xl">
                        </div>
                    @endif

                    <div class="p-5 sm:p-6 flex gap-5 cursor-pointer" @click="open = !open">
                        <div class="shrink-0 pt-1">
                            <div
                                class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $theme['bg'] }} {{ $theme['text'] }} {{ $theme['border'] }} border shadow-inner">
                                @if ($notif->type == 'danger')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @elseif($notif->type == 'success')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($notif->type == 'warning')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-4">
                                <div class="space-y-1">
                                    <h3
                                        class="text-[15px] {{ $isRead ? 'font-semibold text-slate-600' : 'font-bold text-slate-900' }}">
                                        {{ $notif->title }}
                                        @if (!$isRead)
                                            <span
                                                class="inline-block w-2 h-2 ml-2 bg-indigo-500 rounded-full animate-pulse"></span>
                                        @endif
                                    </h3>
                                    <p
                                        class="text-[11px] font-medium text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $notif->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <div
                                    class="shrink-0 text-slate-300 group-hover:text-indigo-400 transition-colors duration-300">
                                    <svg class="w-5 h-5 transition-transform duration-300"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <p class="text-sm text-slate-500 mt-2 leading-relaxed line-clamp-1 group-hover:text-slate-600 transition-colors"
                                x-show="!open">
                                {{ $notif->message }}
                            </p>

                            <div x-show="open" x-collapse>
                                <div
                                    class="pt-2 text-sm text-slate-600 leading-7 border-t border-slate-100 mt-3 border-dashed">
                                    {{ $notif->message }}
                                </div>

                                <div class="flex items-center justify-end gap-3 mt-4 pt-2">
                                    @if (!$isRead)
                                        <form action="{{ route('seller.notifications.read', $notif->id) }}"
                                            method="POST">
                                            @csrf
                                            <button @click.stop type="submit"
                                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @else
                                        <span
                                            class="px-3 py-1.5 text-xs font-bold text-slate-400 bg-slate-50 rounded-lg border border-slate-100 flex items-center gap-1.5 cursor-default">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Sudah dibaca
                                        </span>
                                    @endif

                                    <form action="{{ route('seller.notifications.destroy', $notif->id) }}"
                                        method="POST">
                                        @csrf @method('DELETE')
                                        <button @click.stop type="submit"
                                            class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-slate-400 hover:text-rose-600 bg-transparent hover:bg-rose-50 border border-transparent hover:border-rose-100 rounded-lg transition-colors"
                                            title="Hapus Permanen">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center animate-card"
                    style="animation-delay: 100ms">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-indigo-100 rounded-full blur-xl opacity-50 animate-pulse">
                        </div>
                        <div
                            class="relative bg-white p-6 rounded-full shadow-lg shadow-indigo-100 ring-1 ring-slate-100">
                            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">Semua Bersih!</h3>
                    <p class="text-slate-500 mt-2 max-w-xs mx-auto leading-relaxed">
                        Anda tidak memiliki notifikasi baru. Silakan kembali lagi nanti.
                    </p>
                    <a href="{{ route('seller.dashboard') }}"
                        class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-5 py-2.5 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-10 animate-card" style="animation-delay: 500ms">
            {{ $notifications->links() }}
        </div>
    </div>

</body>

</html>
