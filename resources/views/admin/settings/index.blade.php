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
<body class="bg-gray-50 min-h-screen flex flex-col"
      x-data="{ showPage: false }"
      x-init="setTimeout(() => showPage = true, 100)">

    <div x-show="showPage" x-cloak
         style="display: none;"
         x-transition:enter="transition-all ease-out duration-700"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-98"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         class="flex flex-col min-h-screen">

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

        <main class="flex-1 py-10 px-4 sm:px-6 lg:px-8 pb-24">
            <div class="max-w-5xl mx-auto">
                
                <div class="mb-10">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Identitas Website</h1>
                    <p class="text-gray-500 mt-2 max-w-2xl">
                        Konfigurasi ini akan mempengaruhi tampilan publik (Front-end) website Anda, termasuk logo, favicon, dan informasi kontak.
                    </p>
                </div>

                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                         class="mb-8 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center shadow-sm" x-transition>
                        <div class="bg-green-100 rounded-full p-1 mr-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8" x-data="{ logoPreview: null, faviconPreview: null }">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="p-2 bg-green-50 rounded-lg text-green-600 mr-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Visual & Aset</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">Logo Utama</label>
                                    <div class="flex items-start gap-4">
                                        <div class="w-32 h-32 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 relative group cursor-pointer hover:border-green-500 transition overflow-hidden" @click="$refs.logoInput.click()">
                                            <img :src="logoPreview ? logoPreview : '{{ $setting->site_logo ? asset($setting->site_logo) : 'https://placehold.co/400x400/f3f4f6/9ca3af?text=Logo' }}'" class="w-full h-full object-contain p-2">
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 pt-2">
                                            <button type="button" @click="$refs.logoInput.click()" class="text-sm text-green-600 font-bold hover:underline mb-1">Ganti Logo</button>
                                            <p class="text-xs text-gray-500">Format: PNG/SVG.<br>Max: 2MB.</p>
                                            <input type="file" name="site_logo" x-ref="logoInput" class="hidden" accept="image/*" @change="logoPreview = URL.createObjectURL($event.target.files[0])">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">Favicon</label>
                                    <div class="flex items-start gap-4">
                                        <div class="w-16 h-16 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 relative group cursor-pointer hover:border-green-500 transition overflow-hidden" @click="$refs.faviconInput.click()">
                                            <img :src="faviconPreview ? faviconPreview : '{{ $setting->site_favicon ? asset($setting->site_favicon) : 'https://placehold.co/100x100/f3f4f6/9ca3af?text=ICO' }}'" class="w-8 h-8 object-contain">
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 pt-2">
                                            <button type="button" @click="$refs.faviconInput.click()" class="text-sm text-green-600 font-bold hover:underline mb-1">Ganti Ikon</button>
                                            <p class="text-xs text-gray-500">32x32px. ICO/PNG.</p>
                                            <input type="file" name="site_favicon" x-ref="faviconInput" class="hidden" accept="image/*" @change="faviconPreview = URL.createObjectURL($event.target.files[0])">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 mr-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Informasi Website</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Website</label>
                                    <input type="text" name="site_name" value="{{ $setting->site_name }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white shadow-xs focus:border-blue-500 rounded-xl focus:ring-2 focus:ring-blue-200 outline-none transition font-medium text-gray-900">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tagline / Slogan</label>
                                    <input type="text" name="site_tagline" value="{{ $setting->site_tagline ?? 'Belanja Segar Setiap Hari' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white shadow-xs focus:border-blue-500 rounded-xl focus:ring-2 focus:ring-blue-200 outline-none transition font-medium text-gray-900">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Meta (SEO)</label>
                                    <textarea name="site_description" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white shadow-xs focus:border-blue-500 rounded-xl focus:ring-2 focus:ring-blue-200 outline-none transition resize-none text-gray-900" placeholder="Deskripsi singkat untuk SEO...">{{ $setting->site_description }}</textarea>
                                    <p class="text-xs text-gray-400 mt-2 text-right">Rekomendasi: Maksimal 160 karakter.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="p-2 bg-purple-50 rounded-lg text-purple-600 mr-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Kontak & Sosial Media</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Support</label>
                                    <input type="email" name="contact_email" value="{{ $setting->contact_email }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white shadow-xs focus:border-purple-500 rounded-xl focus:ring-2 focus:ring-purple-200 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Telepon / WhatsApp</label>
                                    <input type="text" name="contact_phone" value="{{ $setting->contact_phone }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white shadow-xs focus:border-purple-500 rounded-xl focus:ring-2 focus:ring-purple-200 outline-none transition">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Kantor</label>
                                    <textarea name="contact_address" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white shadow-xs focus:border-purple-500 rounded-xl focus:ring-2 focus:ring-purple-200 outline-none transition resize-none">{{ $setting->contact_address }}</textarea>
                                </div>
                                <div class="md:col-span-2 border-t border-gray-100 my-2"></div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Link Instagram</label>
                                    <input type="url" name="link_instagram" value="{{ $setting->link_instagram }}" placeholder="https://instagram.com/..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl shadow-xs focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Link Facebook</label>
                                    <input type="url" name="link_facebook" value="{{ $setting->link_facebook }}" placeholder="https://facebook.com/..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl shadow-xs focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none transition">
                                </div>
                            </div>
                        </div>
                    </div>

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
    </div>
</body>
</html>