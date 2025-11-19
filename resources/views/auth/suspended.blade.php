@extends('layouts.auth')
@section('title', 'Akun Ditangguhkan')
@section('heading', 'Akses Akun Dibatasi')

@section('content')
<div class="text-center space-y-4">
    
    <div class="flex justify-center">
        <svg class="w-16 h-16 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
        </svg>
    </div>

    <p class="text-gray-700 text-lg leading-relaxed">
        Akun <span class="text-red-700 font-semibold">Seller</span> Anda telah ditangguhkan.
    </p>

    <div class="bg-red-50 border border-red-200 text-red-800 text-sm p-4 rounded-lg text-left">
        <p class="font-medium">Mengapa ini terjadi?</p>
        <p class="mt-1">Akun Anda ditangguhkan karena terdeteksi adanya pelanggaran terhadap Syarat & Ketentuan layanan kami. Anda tidak dapat mengakses dashboard penjual saat ini.</p>
    </div>

    <p class="text-sm text-gray-500 mt-8">
        Jika Anda merasa ini adalah sebuah kesalahan atau ingin mengajukan banding, 
        silakan hubungi <span class="font-medium text-green-700">Admin Support</span> kami.
    </p>

    <a href="{{ route('auth.logout') }}" 
       class="inline-block w-full py-2.5 px-6 bg-green-100 hover:bg-green-200 text-green-700 font-semibold rounded-lg transition">
        Keluar
    </a>
</div>
@endsection