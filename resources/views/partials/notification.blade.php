@php
    $type = null;
    $message = null;

    if (session('success')) {
        $type = 'success';
        $message = session('success');
    } elseif (session('error') || session('gagal')) {
        $type = 'error';
        $message = session('error') ?? session('gagal');
    } elseif (session('info')) {
        $type = 'info';
        $message = session('info');
    } elseif ($errors->any()) { 
        $type = 'error';
        $message = $errors->first();
    }
@endphp

@if ($type && $message)
    <div 
        x-data="{ 
            show: false, 
            type: '{{ $type }}', 
            message: '{{ addslashes($message) }}' 
        }"
        x-init="
            show = true;
            setTimeout(() => show = false, 5000);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-full"
        
        class="fixed top-6 left-1/2 -translate-x-1/2 w-full max-w-sm sm:max-w-md z-9999 p-4 rounded-lg shadow-2xl border"
        :class="{
            'bg-green-50 border-green-300 text-green-800': type === 'success',
            'bg-red-50 border-red-300 text-red-800': type === 'error',
            'bg-blue-50 border-blue-300 text-blue-800': type === 'info'
        }"
        style="display: none;"
    >
        <div class="flex items-center">
            
            <div class="shrink-0">
                <svg x-show="type === 'success'" class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <svg x-show="type === 'error'" class="w-6 h-6 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1.25-5.25a.75.75 0 001.5 0v-2.5a.75.75 0 00-1.5 0v2.5zM10 6a.75.75 0 000 1.5h.008a.75.75 0 000-1.5H10z" clip-rule="evenodd" />
                </svg>
                <svg x-show="type === 'info'" class="w-6 h-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A.75.75 0 0010 13.5h.253a.25.25 0 01.244.304l-.459 2.066A.75.75 0 0010 16.5h.008a.75.75 0 00.742-.606l.459-2.067a.25.25 0 01.244-.303H11.5a.75.75 0 000-1.5H11a.25.25 0 01-.244-.304l.459-2.067A.75.75 0 0010 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            
            <div class="ml-3 w-0 flex-1">
                <p class="text-sm font-medium leading-tight" x-text="message"></p>
            </div>
            
            <div class="ml-4 shrink-0">
                <button 
                    type="button" 
                    @click="show = false" 
                    class="inline-flex rounded-md p-1.5 transition focus:outline-none"
                    :class="{
                        'text-green-500 hover:bg-green-100': type === 'success',
                        'text-red-500 hover:bg-red-100': type === 'error',
                        'text-blue-500 hover:bg-blue-100': type === 'info'
                    }"
                >
                    <span class="sr-only">Tutup</span>
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif