<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Website - Green Mart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col pb-20">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-green-600 transition group" title="Kembali ke Dashboard">
                        <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <h1 class="text-xl font-bold" style="font-family: 'Fredoka', sans-serif;">
                        <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs font-bold uppercase tracking-wider">Settings</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm border border-green-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Identitas Website</h1>
                <p class="text-gray-500 mt-2 max-w-2xl">
                    Konfigurasi ini akan mempengaruhi tampilan publik (Front-end) website Anda, termasuk logo, favicon, SEO dasar, dan informasi kontak di footer.
                </p>
            </div>

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mb-8 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <div class="bg-green-100 rounded-full p-1 mr-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
                  x-data="{ 
                      logoPreview: null, 
                      faviconPreview: null 
                  }">
                @csrf
                @method('PUT')

                <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-green-50 rounded-lg text-green-600 mr-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Visual & Aset</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-gray-50 rounded-xl p-6 border border-dashed border-gray-300 hover:border-green-400 transition group cursor-pointer" @click="$refs.logoInput.click()">
                                <div class="flex flex-col items-center text-center">
                                    <div class="h-24 w-full flex items-center justify-center mb-4 relative">
                                        <img :src="logoPreview ? logoPreview : '{{ $setting->site_logo ? asset($setting->site_logo) : 'https://placehold.co/400x400/f3f4f6/9ca3af?text=Logo' }}'" 
                                             class="max-h-full max-w-full object-contain drop-shadow-sm">
                                        <div class="absolute inset-0 bg-black/5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded-lg">
                                            <span class="bg-white px-3 py-1 rounded-full text-xs font-bold shadow-sm text-gray-700">Ubah Logo</span>
                                        </div>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-800">Logo Utama</h4>
                                    <p class="text-xs text-gray-500 mt-1">Format PNG (Transparan). Max 2MB.</p>
                                    <input type="file" name="site_logo" x-ref="logoInput" class="hidden" accept="image/*" @change="logoPreview = URL.createObjectURL($event.target.files[0])">
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6 border border-dashed border-gray-300 hover:border-green-400 transition group cursor-pointer" @click="$refs.faviconInput.click()">
                                <div class="flex flex-col items-center text-center">
                                    <div class="h-24 w-24 flex items-center justify-center mb-4 relative bg-white rounded-lg shadow-sm border border-gray-100">
                                        <img :src="faviconPreview ? faviconPreview : '{{ $setting->site_favicon ? asset($setting->site_favicon) : 'https://placehold.co/100x100/f3f4f6/9ca3af?text=ICO' }}'" 
                                             class="w-12 h-12 object-contain">
                                        <div class="absolute inset-0 bg-black/5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded-lg">
                                            <span class="bg-white px-2 py-1 rounded text-xs font-bold shadow-sm text-gray-700">Ubah</span>
                                        </div>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-800">Favicon</h4>
                                    <p class="text-xs text-gray-500 mt-1">Ikon tab browser. 32x32 atau 64x64 px.</p>
                                    <input type="file" name="site_favicon" x-ref="faviconInput" class="hidden" accept="image/*" @change="faviconPreview = URL.createObjectURL($event.target.files[0])">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-blue-50 rounded-lg text-blue-600 mr-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Informasi & SEO</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Website</label>
                                <input type="text" name="site_name" value="{{ $setting->site_name }}" 
                                       class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white border focus:border-blue-500 rounded-xl focus:ring-2 focus:ring-blue-200 outline-none transition font-medium text-gray-900">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tagline / Slogan</label>
                                <input type="text" name="site_tagline" value="{{ $setting->site_tagline }}" 
                                       class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white border focus:border-blue-500 rounded-xl focus:ring-2 focus:ring-blue-200 outline-none transition font-medium text-gray-900">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Meta (SEO)</label>
                                <textarea name="site_description" rows="3" 
                                          class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white border focus:border-blue-500 rounded-xl focus:ring-2 focus:ring-blue-200 outline-none transition resize-none text-gray-900"
                                          placeholder="Deskripsi singkat website untuk mesin pencari...">{{ $setting->site_description }}</textarea>
                                <p class="text-xs text-gray-400 mt-2 text-right">Rekomendasi: Maksimal 160 karakter.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-purple-50 rounded-lg text-purple-600 mr-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Kontak & Footer</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Resmi</label>
                                <input type="email" name="contact_email" value="{{ $setting->contact_email }}" 
                                       class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white border focus:border-purple-500 rounded-xl focus:ring-2 focus:ring-purple-200 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Telepon / WhatsApp</label>
                                <input type="text" name="contact_phone" value="{{ $setting->contact_phone }}" 
                                       class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white border focus:border-purple-500 rounded-xl focus:ring-2 focus:ring-purple-200 outline-none transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Fisik</label>
                                <textarea name="contact_address" rows="2" class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white border focus:border-purple-500 rounded-xl focus:ring-2 focus:ring-purple-200 outline-none transition resize-none">{{ $setting->contact_address }}</textarea>
                            </div>
                            
                            <div class="md:col-span-2 border-t border-gray-100 pt-4">
                                <h4 class="text-sm font-bold text-gray-500 uppercase mb-4">Sosial Media</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </span>
                                        <input type="url" name="link_instagram" value="{{ $setting->link_instagram }}" placeholder="https://instagram.com/..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none transition">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </span>
                                        <input type="url" name="link_facebook" value="{{ $setting->link_facebook }}" placeholder="https://facebook.com/..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none transition">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="fixed bottom-6 left-0 right-0 flex justify-center z-40 pointer-events-none">
                    <div class="bg-gray-900/90 backdrop-blur-md text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-6 pointer-events-auto transform transition hover:scale-105">
                        <span class="text-sm text-gray-300">Simpan perubahan sekarang?</span>
                        <button type="submit" class="bg-green-500 hover:bg-green-400 text-gray-900 font-bold py-2 px-6 rounded-full transition shadow-lg">
                            Simpan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </main>

</body>
</html>