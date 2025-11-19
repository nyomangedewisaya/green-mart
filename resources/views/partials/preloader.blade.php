<div x-data="{ show: true }"
     x-init="
        // Hilangkan preloader setelah halaman dimuat sepenuhnya
        // Ditambah sedikit delay (800ms) agar animasinya sempat terlihat dan tidak kedip
        window.onload = () => {
            setTimeout(() => { show = false }, 800);
        }
     "
     x-show="show"
     x-transition:leave="transition ease-in-out duration-500"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-white"
     style="display: none;"> {{-- style ini mencegah kedip sebelum Alpine jalan --}}

    <div class="flex flex-col items-center justify-center space-y-4">
        
        <div class="relative flex items-center justify-center">
            
            <div class="w-24 h-24 border-4 border-gray-100 rounded-full absolute"></div>
            <div class="w-24 h-24 border-4 border-transparent border-t-green-500 border-b-amber-400 rounded-full animate-spin absolute"></div>
            
            <div class="text-3xl animate-pulse relative z-10" style="font-family: 'Fredoka', sans-serif;">
                🌿
            </div>
        </div>

        <div class="text-center">
            <h1 class="text-xl font-bold tracking-wide" style="font-family: 'Fredoka', sans-serif;">
                <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
            </h1>
            <p class="text-xs text-gray-400 mt-1 font-medium tracking-widest uppercase">Loading...</p>
        </div>
    </div>
</div>