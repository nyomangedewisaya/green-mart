@extends('layouts.auth')
@section('title', 'Daftar')
@section('heading', 'Buat akun baru')
@section('auth-width', 'max-w-2xl')

@section('content')
    <form method="POST" action="{{ route('auth.register.post') }}" class="space-y-5" x-data="{ showPassword: false, avatarPreview: null, role: 'buyer' }"
        enctype="multipart/form-data">
        @csrf

        <div class="flex flex-col items-center space-y-2">
            <label class="block text-sm font-medium text-gray-700">Foto Profil (Opsional)</label>

            <div @click="$refs.avatarInput.click()"
                class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-200 transition-all border-2 border-dashed border-gray-300">

                <template x-if="avatarPreview">
                    <img :src="avatarPreview" alt="Avatar Preview" class="w-full h-full rounded-full object-cover">
                </template>

                <template x-if="!avatarPreview">
                    <div class="text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.008 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span class="text-xs font-medium">Upload Foto</span>
                    </div>
                </template>
            </div>

            <input type="file" name="avatar" x-ref="avatarInput"
                @change="avatarPreview = URL.createObjectURL($event.target.files[0])" class="hidden" accept="image/*">
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-5">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A1.5 1.5 0 0118 21.75H6.A1.5 1.5 0 014.501 20.118z" />
                            </svg>
                        </span>
                        <input type="text" name="name" required placeholder="Masukkan nama lengkap"
                            class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:shadow-lg focus:shadow-green-500/20 focus:outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <input type="email" name="email" required placeholder="nama@email.com"
                            class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:shadow-lg focus:shadow-green-500/20 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            placeholder="Buat kata sandi"
                            class="w-full pl-11 pr-12 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:shadow-lg focus:shadow-green-500/20 focus:outline-none transition-all">

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-500 hover:text-green-600">
                            <svg x-show="!showPassword" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.21 6.21A10.477 10.477 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.498a10.522 10.522 0 01-4.293 5.575M5.25 5.25l13.5 13.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Daftar Sebagai</label>

                    <input type="hidden" name="role" :value="role" required>

                    <div class="flex items-center rounded-lg border border-gray-300 p-0.5 bg-gray-50">
                        <button type="button" @click="role = 'buyer'"
                            :class="role === 'buyer' ? 'bg-green-600 text-white shadow-md' :
                                'text-gray-700 hover:bg-gray-100'"
                            class="w-1/2 py-2.5 rounded-md text-sm font-medium transition-all">
                            Buyer (Pembeli)
                        </button>

                        <button type="button" @click="role = 'seller'"
                            :class="role === 'seller' ? 'bg-green-600 text-white shadow-md' :
                                'text-gray-700 hover:bg-gray-100'"
                            class="w-1/2 py-2.5 rounded-md text-sm font-medium transition-all">
                            Seller (Penjual)
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <button
            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg shadow-lg shadow-green-600/30 hover:shadow-xl hover:shadow-green-600/40 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            Daftar Sekarang
        </button>

        <p class="text-center text-sm text-gray-600 pt-2">
            Sudah punya akun?
            <a href="{{ route('auth.login') }}" class="text-green-700 font-semibold hover:underline">Masuk</a>
        </p>
    </form>
@endsection
