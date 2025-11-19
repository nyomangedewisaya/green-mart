@extends('layouts.auth')
@section('title', 'Menunggu Persetujuan')
@section('heading', 'Hampir Selesai!')

@section('content')
<div class="text-center space-y-4">
    
    <div class="flex justify-center">
        <svg class="w-16 h-16 text-green-600 animate-spin" style="animation-duration: 3s;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    <p class="text-gray-700 text-lg leading-relaxed">
        Terima kasih telah mendaftar sebagai <span class="text-green-700 font-semibold">Seller</span>.
        Akun Anda selangkah lagi siap digunakan.
    </p>

    <div classs="text-left w-full my-4">
        <ol class="space-y-4">
            <li class="flex items-center text-green-700">
                <span class="shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold">✓</span>
                <span class="ml-3 font-medium">Pendaftaran Diterima</span>
            </li>
            
            <li class="flex items-center text-yellow-700">
                <span class="shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center animate-pulse font-bold">!</span>
                <span class="ml-3 font-medium">Verifikasi Data oleh Admin</span>
            </li>

            <li class="flex items-center text-gray-500">
                <span class="shrink-0 w-8 h-8 bg-gray-300 text-gray-700 rounded-full flex items-center justify-center font-bold">3</span>
                <span class="ml-3 font-medium">Aktivasi Akun</span>
            </li>
        </ol>
    </div>

    <p class="text-sm text-gray-500 mt-8">
        Proses ini biasanya memakan waktu 1x24 jam. Kami akan mengirimkan email 
        konfirmasi ke alamat Anda setelah akun berhasil diaktifkan.
    </p>

    <a href="{{ route('auth.logout') }}" 
       class="inline-block w-full py-2.5 px-6 bg-green-100 hover:bg-green-200 text-green-700 font-semibold rounded-lg transition">
        Keluar
    </a>
</div>
@endsection