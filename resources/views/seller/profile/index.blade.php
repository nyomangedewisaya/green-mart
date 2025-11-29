<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - {{ $seller->name }}</title>
    
    {{-- Tailwind & Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .input-focus:focus { 
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); 
            border-color: #22c55e; 
        }
        .page-transition {
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800 selection:bg-green-100 selection:text-green-700"
      x-data="{ loaded: false }" 
      x-init="setTimeout(() => loaded = true, 50)">

    {{-- HEADER --}}
    <div class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/80">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.dashboard') }}" class="group p-2 -ml-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-full transition-all">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-lg font-bold text-gray-900">Pengaturan Toko</h1>
            </div>
            <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-green-700 bg-green-100 hover:bg-green-200 px-4 py-2 rounded-lg transition-colors">
                Selesai
            </a>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10 page-transition opacity-0 translate-y-8"
         :class="{ 'opacity-100 translate-y-0': loaded }"
         x-data="{ activeTab: 'store' }">

        {{-- ALERT --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mb-6 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg shadow-emerald-200/50">
                <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- SIDEBAR NAVIGATION --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Quick Profile Card --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden group">
                    {{-- Mini Banner Preview --}}
                    <div class="h-24 bg-gray-200 relative">
                        @if($seller->banner)
                            <img src="{{ asset($seller->banner) }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition">
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-green-400 to-teal-500"></div>
                        @endif
                    </div>
                    <div class="px-6 pb-6 text-center relative">
                        <div class="-mt-12 mb-3 relative inline-block">
                            <div class="w-24 h-24 rounded-full p-1 bg-white shadow-md">
                                @if($seller->logo)
                                    <img src="{{ asset($seller->logo) }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <div class="w-full h-full bg-green-50 text-green-600 rounded-full flex items-center justify-center font-bold text-3xl">{{ substr($seller->name, 0, 1) }}</div>
                                @endif
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $seller->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-2 space-y-1">
                    <button @click="activeTab = 'store'"
                        :class="activeTab === 'store' ? 'bg-green-50 text-green-700 shadow-sm ring-1 ring-green-100' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-left">
                        <svg class="w-5 h-5" :class="activeTab === 'store' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <div><span>Profil Toko</span><p class="text-[10px] font-normal opacity-70 mt-0.5">Banner, Logo & Info</p></div>
                    </button>

                    <button @click="activeTab = 'account'"
                        :class="activeTab === 'account' ? 'bg-green-50 text-green-700 shadow-sm ring-1 ring-green-100' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-left">
                        <svg class="w-5 h-5" :class="activeTab === 'account' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <div><span>Akun & Keamanan</span><p class="text-[10px] font-normal opacity-70 mt-0.5">Login & Password</p></div>
                    </button>
                </div>
            </div>

            {{-- CONTENT FORMS --}}
            <div class="lg:col-span-8">
                
                {{-- TAB 1: PROFIL TOKO --}}
                <div x-show="activeTab === 'store'" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    
                    <form action="{{ route('seller.profile.updateStore') }}" method="POST" enctype="multipart/form-data" 
                          class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        @csrf @method('PUT')

                        {{-- BANNER AREA (NEW) --}}
                        <div class="relative w-full h-48 bg-gray-100 group">
                            {{-- Image Preview --}}
                            <img id="preview-banner" src="{{ $seller->banner ? asset($seller->banner) : '' }}" 
                                 class="{{ $seller->banner ? '' : 'hidden' }} w-full h-full object-cover transition duration-500">
                            
                            {{-- Placeholder jika kosong --}}
                            <div id="placeholder-banner" class="{{ $seller->banner ? 'hidden' : 'flex' }} w-full h-full flex-col items-center justify-center text-gray-400 bg-gradient-to-br from-gray-100 to-gray-200">
                                <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-semibold">Upload Banner Toko (1200x400)</span>
                            </div>

                            {{-- Overlay Upload Button --}}
                            <label class="absolute top-4 right-4 bg-white/90 backdrop-blur text-gray-700 hover:text-green-600 px-3 py-1.5 rounded-lg shadow-sm cursor-pointer border border-white/50 transition flex items-center gap-2 text-xs font-bold z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Ganti Banner
                                <input type="file" name="banner" class="hidden" onchange="previewImage(event, 'preview-banner', 'placeholder-banner')">
                            </label>
                        </div>

                        {{-- LOGO & BASIC INFO --}}
                        <div class="px-8 pb-8">
                            <div class="flex flex-col sm:flex-row gap-6 items-end -mt-10 mb-6 relative z-10">
                                {{-- Logo Upload --}}
                                <div class="shrink-0 relative group">
                                    <div class="w-28 h-28 rounded-2xl bg-white p-1 shadow-lg border border-gray-100 overflow-hidden relative">
                                        <img id="preview-logo" src="{{ $seller->logo ? asset($seller->logo) : '' }}" class="{{ $seller->logo ? '' : 'hidden' }} w-full h-full object-cover rounded-xl">
                                        <div id="placeholder-logo" class="{{ $seller->logo ? 'hidden' : 'flex' }} w-full h-full bg-gray-50 rounded-xl flex-col items-center justify-center text-gray-400">
                                            <span class="text-[10px] font-bold">Logo</span>
                                        </div>
                                        
                                        {{-- Hover Overlay Logo --}}
                                        <label class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer rounded-xl text-white">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <input type="file" name="logo" class="hidden" onchange="previewImage(event, 'preview-logo', 'placeholder-logo')">
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="flex-1 pb-2">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $seller->name }}</h2>
                                    <p class="text-sm text-gray-500 mt-0.5">Edit detail toko dan informasi kontak.</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Toko <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $seller->name) }}" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none input-focus bg-white text-gray-900 font-medium" placeholder="Nama Toko">
                                    <p class="text-xs text-gray-400 mt-1">Slug URL akan otomatis berubah jika nama diganti.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nomor Telepon / WhatsApp <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone', $seller->phone) }}" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none input-focus bg-white text-gray-900 font-medium">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                                    <textarea name="address" rows="3" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none input-focus bg-white text-gray-900 font-medium">{{ old('address', $seller->address) }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Deskripsi Singkat</label>
                                    <textarea name="description" rows="4"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none input-focus bg-white text-gray-900 font-medium" placeholder="Ceritakan tentang toko Anda...">{{ old('description', $seller->description) }}</textarea>
                                </div>
                            </div>

                            <div class="pt-8 mt-6 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="px-8 py-3.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Simpan Profil
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TAB 2: AKUN USER --}}
                <div x-show="activeTab === 'account'" style="display: none;"
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    
                    <form action="{{ route('seller.profile.updateAccount') }}" method="POST" 
                          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
                        @csrf @method('PUT')

                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-100">Pengaturan Akun</h2>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Pemilik <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none input-focus bg-white text-gray-900 font-medium">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none input-focus bg-white text-gray-900 font-medium">
                                </div>
                            </div>

                            <div class="bg-amber-50 rounded-xl p-6 border border-amber-100 mt-4">
                                <h3 class="text-sm font-bold text-amber-900 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Ganti Password
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-amber-800 mb-1">Password Baru</label>
                                        <input type="password" name="password" 
                                            class="w-full px-4 py-2.5 rounded-xl border border-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white placeholder-gray-400" placeholder="Minimal 6 karakter">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-amber-800 mb-1">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" 
                                            class="w-full px-4 py-2.5 rounded-xl border border-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white placeholder-gray-400" placeholder="Ketik ulang password">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="px-8 py-3.5 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                                    Simpan Akun
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Universal Preview Image Function
        function previewImage(event, previewId, placeholderId) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(previewId);
                    const ph = document.getElementById(placeholderId);
                    
                    if(img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                    if(ph) {
                        ph.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>