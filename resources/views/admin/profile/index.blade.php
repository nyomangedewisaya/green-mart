<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Green Mart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col" x-data="{ showPage: false }" x-init="setTimeout(() => showPage = true, 100)">

    <div x-show="showPage" x-cloak style="display: none;" x-transition:enter="transition-all ease-out duration-700"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="flex flex-col min-h-screen">

        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.dashboard') }}"
                            class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-green-600 transition"
                            title="Kembali ke Dashboard">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-xl font-bold" style="font-family: 'Fredoka', sans-serif;">
                            <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
                        </h1>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500 mr-2">Login sebagai</span>
                        <span class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1 py-10 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">

                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Pengaturan Akun</h2>
                    <p class="text-gray-500 mt-2">Kelola informasi profil dan keamanan akun Anda.</p>
                </div>

                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.duration.500ms
                        class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data"
                    x-data="{ photoPreview: null, showPass: false }">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="relative h-48 bg-green-600">
                            <div class="absolute inset-0 bg-black/10"></div>
                        </div>

                        <div class="px-8 pb-8">
                            <div class="relative -mt-20 mb-6 flex justify-center sm:justify-start">
                                <div class="relative group cursor-pointer" @click="$refs.photoInput.click()">
                                    <div
                                        class="w-40 h-40 rounded-full border-4 border-white bg-white shadow-md overflow-hidden">

                                        <img :src="photoPreview ? photoPreview :
                                            '{{ $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar)) : 'https://placehold.co/200x200/e0e0e0/757575?text=' . substr($user->name, 0, 1) }}'"
                                            class="w-full h-full object-cover transition group-hover:scale-105 duration-300">
                                    </div>

                                    <div
                                        class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200">
                                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <input type="file" name="avatar" x-ref="photoInput" class="hidden" accept="image/*"
                                    @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                                <div class="space-y-6">
                                    <div class="border-b border-gray-100 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Informasi Dasar
                                        </h3>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                            required
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white transition outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email
                                            Address</label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                            required
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white transition outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Role /
                                            Jabatan</label>
                                        <input type="text" value="Super Administrator" disabled
                                            class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed">
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="border-b border-gray-100 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Ubah Password
                                        </h3>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Password
                                            Lama</label>
                                        <input :type="showPass ? 'text' : 'password'" name="current_password"
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white transition outline-none"
                                            placeholder="• • • • • • • •">
                                        @error('current_password')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Password
                                                Baru</label>
                                            <input :type="showPass ? 'text' : 'password'" name="password"
                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white transition outline-none"
                                                placeholder="Min. 8 karakter">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi</label>
                                            <input :type="showPass ? 'text' : 'password'" name="password_confirmation"
                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white transition outline-none"
                                                placeholder="Ulangi password">
                                        </div>
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror

                                    <div class="flex items-center mt-2">
                                        <input type="checkbox" id="togglePass"
                                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                            @click="showPass = !showPass">
                                        <label for="togglePass"
                                            class="ml-2 text-sm text-gray-600 cursor-pointer select-none">Lihat
                                            Password</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex items-center justify-between">
                            <p class="text-sm text-gray-500 italic">Terakhir diperbarui:
                                {{ $user->updated_at->translatedFormat('d F Y') }}</p>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-white transition">Batal</a>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5">Simpan
                                    Perubahan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>
