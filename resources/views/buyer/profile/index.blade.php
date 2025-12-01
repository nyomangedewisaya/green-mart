@extends('layouts.buyer')
@section('title', 'Profil Saya')

@section('content')

{{-- WRAPPER dengan Alpine JS untuk Image Preview --}}
<div x-data="{ 
    photoName: null,
    photoPreview: null,
    
    updatePreview() {
        const file = this.$refs.photo.files[0];
        if (!file) return;
        this.photoName = file.name;
        const reader = new FileReader();
        reader.onload = (e) => { this.photoPreview = e.target.result; };
        reader.readAsDataURL(file);
    }
}" class="min-h-screen bg-gray-50/50 py-10 font-inter">

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             class="fixed top-24 right-4 z-[999] bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
            <div class="bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif
    
    {{-- ALERT ERROR --}}
    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" 
             class="fixed top-24 right-4 z-[999] bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
            <div class="bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>
            <span class="font-medium text-sm">Gagal menyimpan. Periksa input Anda.</span>
        </div>
    @endif

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- HEADER --}}
        <div class="flex items-center gap-4 mb-8 animate-fade-up" style="animation-delay: 0ms;">
            <a href="{{ route('buyer.home') }}" class="p-2.5 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-green-600 hover:border-green-200 transition shadow-sm group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Profil Saya</h1>
                <p class="text-sm text-gray-500">Kelola informasi pribadi dan keamanan akun Anda.</p>
            </div>
        </div>

        <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- KOLOM KIRI: AVATAR & INFO SINGKAT --}}
                <div class="lg:col-span-4 space-y-6 animate-fade-up" style="animation-delay: 100ms;">
                    
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-8 text-center relative overflow-hidden group">
                        {{-- Background Decoration --}}
                        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-b from-green-50 to-white"></div>
                        
                        <div class="relative z-10">
                            {{-- Avatar Preview --}}
                            <div class="relative inline-block">
                                <div class="w-32 h-32 rounded-full p-1 bg-white border-2 border-green-100 shadow-md overflow-hidden mx-auto">
                                    <img x-show="!photoPreview" 
                                         src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=10b981&color=fff' }}" 
                                         class="w-full h-full object-cover rounded-full">
                                    
                                    <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover rounded-full" style="display: none;">
                                </div>

                                {{-- Upload Trigger --}}
                                <button type="button" @click="$refs.photo.click()" 
                                        class="absolute bottom-1 right-1 p-2 bg-green-600 text-white rounded-full hover:bg-green-700 shadow-lg transition transform hover:scale-110 border-2 border-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                
                                {{-- Hidden Input --}}
                                <input type="file" name="avatar" x-ref="photo" class="hidden" accept="image/*" @change="updatePreview()">
                            </div>

                            <h3 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            
                            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Member Aktif
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                        <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tips Keamanan
                        </h4>
                        <p class="text-xs text-green-700 leading-relaxed">
                            Gunakan password yang kuat dan jangan pernah bagikan data login Anda kepada siapapun. Kami menjaga privasi data Anda dengan enkripsi.
                        </p>
                    </div>

                </div>

                {{-- KOLOM KANAN: FORM EDIT --}}
                <div class="lg:col-span-8 space-y-6 animate-fade-up" style="animation-delay: 200ms;">
                    
                    {{-- Section 1: Data Diri --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-6 sm:p-8">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Informasi Pribadi
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nomor Telepon</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></span>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxx"
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm font-medium">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Alamat Lengkap</label>
                            <div class="relative">
                                <span class="absolute top-3 left-3 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                                <textarea name="address" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm font-medium">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Keamanan --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-6 sm:p-8">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Keamanan (Opsional)
                        </h2>
                        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 mb-6 text-xs text-yellow-800">
                            Kosongkan jika tidak ingin mengubah password.
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Password Baru</label>
                                <input type="password" name="password" placeholder="Minimal 6 karakter"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm font-medium">
                            </div>
                        </div>
                    </div>

                    {{-- Footer Action --}}
                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<style>
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-down { animation: fadeInDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

@endsection