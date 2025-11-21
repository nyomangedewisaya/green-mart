<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Green Mart Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scroll:hover::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex overflow-hidden" x-data="chatSystem()">

    <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full z-20 shrink-0 shadow-lg">
        <div class="h-16 px-4 flex items-center justify-between border-b border-gray-100 bg-white">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                    class="p-2 rounded-lg hover:bg-gray-50 text-gray-400 hover:text-green-600 transition"
                    title="Dashboard">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-gray-800">Pesan</h1>
            </div>
            <button @click="showSearchModal = true; $nextTick(() => $refs.searchInput.focus());"
                class="p-2 bg-green-50 text-green-600 rounded-full hover:bg-green-600 hover:text-white transition shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>

        <div class="p-3 border-b border-gray-50">
            <input type="text" x-model="searchQuery" placeholder="Filter kontak..."
                class="w-full pl-4 pr-4 py-2 bg-gray-100 border-transparent focus:bg-white focus:ring-2 focus:ring-green-500 rounded-lg text-sm transition outline-none">
        </div>

        <div class="flex-1 overflow-y-auto custom-scroll px-2 pb-2 space-y-1">
            <template x-for="user in filteredUsers" :key="user.id">
                <div @click="selectUser(user)"
                    class="group flex items-center gap-3 p-3 rounded-xl cursor-pointer transition border border-transparent"
                    :class="activeUser?.id === user.id ? 'bg-green-50 border-green-100' : 'hover:bg-gray-50'">

                    <div class="relative shrink-0">
                        <div
                            class="w-11 h-11 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm overflow-hidden border border-gray-100">
                            <img x-show="getAvatar(user)" :src="getAvatar(user)" class="w-full h-full object-cover">
                            <span x-show="!getAvatar(user)" x-text="user.name.substring(0,2).toUpperCase()"></span>
                        </div>
                        <span x-show="user.is_online"
                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full shadow-sm"></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-0.5">
                            <h3 class="text-sm font-semibold text-gray-900 truncate" x-text="user.name"></h3>
                            <span class="text-[10px] text-gray-400"
                                x-text="formatTimeShort(user.last_message_at)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-xs text-gray-500 truncate w-32 group-hover:text-gray-700">Buka percakapan...
                            </p>
                            <div x-show="user.unread_count > 0"
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-bold text-white bg-green-600 rounded-full shadow-sm"
                                x-text="user.unread_count > 99 ? '99+' : user.unread_count">
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex-1 flex flex-col bg-[#f0f2f5] relative">

        <div x-show="!activeUser"
            class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 z-0">
            <div class="w-20 h-20 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 text-green-600">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Green Mart Chat</h2>
            <p class="text-gray-500 mt-2">Pilih kontak untuk mulai mengirim pesan.</p>
        </div>

        <div x-show="activeUser" class="flex-1 flex flex-col h-full z-10 relative" style="display: none;">

            <div class="h-16 px-6 flex items-center justify-between bg-white border-b border-gray-200 shadow-sm z-20">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden border border-gray-200 flex items-center justify-center text-gray-500 font-bold">
                        <img x-show="getAvatar(activeUser)" :src="getAvatar(activeUser)"
                            class="w-full h-full object-cover">
                        <span x-show="!getAvatar(activeUser)"
                            x-text="activeUser?.name?.substring(0,2).toUpperCase()"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 leading-tight" x-text="activeUser?.name"></h3>
                        <p class="text-xs text-green-600 font-medium flex items-center">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"
                                x-show="activeUser?.is_online"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"
                                x-show="!activeUser?.is_online"></span>
                            <span x-text="activeUser?.is_online ? 'Online' : 'Offline'"></span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="confirmClearChat()"
                        class="p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-full transition"
                        title="Hapus Percakapan">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                    <button class="p-2 text-gray-400 hover:bg-gray-100 rounded-full transition" title="Info">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scroll" x-ref="chatContainer"
                style="background-color: #efeae2; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;">

                <template x-for="chat in chats" :key="chat.id">
                    <div class="flex w-full" :class="chat.sender_id == myId ? 'justify-end' : 'justify-start'">

                        <div class="max-w-[70%] min-w-[100px] relative shadow-sm rounded-lg p-1"
                            :class="chat.sender_id == myId ? 'bg-[#d9fdd3] rounded-tr-none' :
                                'bg-white rounded-tl-none border border-gray-200'">

                            <div class="px-3 py-2 text-sm text-gray-900 leading-relaxed wrap-break-word">
                                <span x-text="chat.message"></span>
                            </div>

                            <div class="flex items-center justify-end gap-1 px-1 pb-0.5 select-none">
                                <span class="text-[10px] text-gray-500" x-text="formatTime(chat.created_at)"></span>

                                <template x-if="chat.sender_id == myId">
                                    <div class="ml-0.5">
                                        <svg x-show="chat.is_read" class="w-3.5 h-3.5 text-blue-500"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M11.602 13.7599L13.014 15.1719L21.4795 6.7063L22.8938 8.12051L13.014 18.0003L6.65 11.6363L8.06421 10.2221L10.1855 12.3434L11.602 13.7599ZM11.6037 10.9322L16.5563 5.97949L17.9666 7.38977L13.014 12.3424L11.6037 10.9322ZM8.77698 16.5873L7.36396 18.0003L1 11.6363L2.41421 10.2221L3.82723 11.6352L3.82604 11.6363L8.77698 16.5873Z">
                                            </path>
                                        </svg>

                                        <svg x-show="!chat.is_read" class="w-3.5 h-3.5 text-gray-400"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M11.602 13.7599L13.014 15.1719L21.4795 6.7063L22.8938 8.12051L13.014 18.0003L6.65 11.6363L8.06421 10.2221L10.1855 12.3434L11.602 13.7599ZM11.6037 10.9322L16.5563 5.97949L17.9666 7.38977L13.014 12.3424L11.6037 10.9322ZM8.77698 16.5873L7.36396 18.0003L1 11.6363L2.41421 10.2221L3.82723 11.6352L3.82604 11.6363L8.77698 16.5873Z">
                                            </path>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-3 bg-gray-100 border-t border-gray-200">
                <form @submit.prevent="sendMessage" class="flex items-end gap-2">
                    <div
                        class="flex-1 bg-white rounded-2xl border border-gray-300 focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 flex items-center px-4 py-2.5 transition shadow-sm">
                        <input type="text" x-model="newMessage"
                            class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-800 placeholder-gray-400"
                            placeholder="Ketik pesan..." autofocus>
                    </div>
                    <button type="submit" :disabled="!newMessage.trim()"
                        class="p-3 rounded-full bg-green-600 text-white hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition shadow-md flex items-center justify-center transform active:scale-95">
                        <svg class="w-5 h-5 translate-x-0.5 -translate-y-0.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="showSearchModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showSearchModal = false"></div>
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl z-50 overflow-hidden flex flex-col max-h-[80vh]">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Chat Baru</h3>
                <button @click="showSearchModal = false" class="text-gray-400 hover:text-red-500"><svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>
            <div class="flex border-b border-gray-100 text-sm font-medium text-center">
                <button @click="searchType = 'all'; searchNewUser()" class="flex-1 py-3 hover:bg-gray-50"
                    :class="searchType == 'all' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500'">Semua</button>
                <button @click="searchType = 'seller'; searchNewUser()" class="flex-1 py-3 hover:bg-gray-50"
                    :class="searchType == 'seller' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500'">Seller</button>
                <button @click="searchType = 'buyer'; searchNewUser()" class="flex-1 py-3 hover:bg-gray-50"
                    :class="searchType == 'buyer' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500'">Buyer</button>
            </div>
            <div class="p-3">
                <input type="text" x-ref="searchInput" x-model="userSearchQuery"
                    @input.debounce.300ms="searchNewUser()" placeholder="Ketik nama user..."
                    class="w-full px-4 py-2 bg-gray-100 border-transparent rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 outline-none text-sm">
            </div>
            <div class="flex-1 overflow-y-auto p-2 custom-scroll">
                <template x-for="result in searchResults" :key="result.id">
                    <div @click="startChat(result)"
                        class="flex items-center gap-3 p-2 hover:bg-green-50 rounded-lg cursor-pointer transition">
                        <div
                            class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500 overflow-hidden border border-gray-100">
                            <img x-show="getAvatar(result)" :src="getAvatar(result)"
                                class="w-full h-full object-cover">
                            <span x-show="!getAvatar(result)"
                                x-text="result.name ? result.name.substring(0,2).toUpperCase() : '??'"></span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 text-sm" x-text="result.name"></h4>
                            <p class="text-xs text-gray-500 capitalize" x-text="result.role"></p>
                        </div>
                        <span x-show="result.is_online" class="w-2 h-2 bg-green-500 rounded-full"></span>
                    </div>
                </template>
                <div x-show="searchResults.length === 0" class="text-center py-6 text-gray-400 text-xs">Tidak
                    ditemukan.</div>
            </div>
        </div>
    </div>

    <div x-show="showClearModal" class="fixed inset-0 z-99 flex items-center justify-center p-4"
        style="display: none;">

        <div x-show="showClearModal" @click="showClearModal = false"
            class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
        </div>

        <div x-show="showClearModal" class="relative w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900">Hapus Percakapan?</h3>

                <div class="mt-4 text-base text-gray-600">
                    Semua pesan dengan <strong class="text-gray-900" x-text="activeUser?.name"></strong> akan dihapus
                    dari tampilan Anda.
                </div>

                <div class="mt-4 bg-red-50 border border-red-100 rounded-lg p-3 text-sm text-red-600 text-left">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pesan <strong>tidak akan hilang</strong> dari sisi lawan bicara, kecuali mereka juga
                            menghapusnya.</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-xl">
                <button @click="performClearChat()" type="button"
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition">
                    Ya, Hapus Semua
                </button>
                <button @click="showClearModal = false" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        function chatSystem() {
            return {
                myId: {{ Auth::id() }},
                users: @json($users),
                searchQuery: '',
                activeUser: null,
                chats: [],
                newMessage: '',

                showSearchModal: false,
                showClearModal: false,
                searchType: 'all',
                userSearchQuery: '',
                searchResults: [],

                polling: null,

                getAvatar(user) {
                    if (!user || !user.avatar) return null;
                    if (user.avatar.startsWith('http')) return user.avatar;
                    return '/storage/' + user.avatar;
                },

                get filteredUsers() {
                    if (!this.searchQuery) return this.users;
                    return this.users.filter(u => u.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
                },

                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                formatTimeShort(dateString) {
                    if (!dateString) return '';
                    const d = new Date(dateString);
                    const now = new Date();
                    if (d.toDateString() === now.toDateString()) {
                        return d.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                    return d.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short'
                    });
                },

                selectUser(user) {
                    this.activeUser = user;
                    this.chats = [];
                    this.loadChats();
                    this.startPolling();

                    const u = this.users.find(c => c.id === user.id);
                    if (u) u.unread_count = 0;
                },

                loadChats(scroll = true) {
                    if (!this.activeUser) return;

                    fetch(`{{ url('admin/chat/history') }}/${this.activeUser.id}`)
                        .then(r => r.json())
                        .then(data => {
                            const isNew = data.length > this.chats.length;
                            this.chats = data;
                            if (scroll || isNew) this.scrollToBottom();
                        });
                },

                sendMessage() {
                    if (!this.newMessage.trim()) return;

                    const payload = {
                        receiver_id: this.activeUser.id,
                        message: this.newMessage
                    };

                    const temp = {
                        id: Date.now(),
                        sender_id: this.myId,
                        message: this.newMessage,
                        created_at: new Date().toISOString(),
                        is_read: false
                    };

                    this.chats.push(temp);
                    this.newMessage = '';
                    this.scrollToBottom();

                    const token = document.querySelector('meta[name="csrf-token"]').content;

                    fetch("{{ route('admin.chats.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": token
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal kirim');
                            return res.json();
                        })
                        .then(data => {
                            this.loadChats(false);
                        })
                        .catch(err => console.error(err));
                },

                // Fitur Modal Cari User Baru
                searchNewUser() {
                    fetch(`{{ route('chats.search_user') }}?q=${this.userSearchQuery}&type=${this.searchType}`)
                        .then(r => r.json())
                        .then(data => this.searchResults = data);
                },

                startChat(user) {
                    let exists = this.users.find(u => u.id === user.id);
                    if (!exists) {
                        this.users.unshift(user);
                    }
                    this.showSearchModal = false;
                    this.selectUser(user);
                },

                startPolling() {
                    if (this.polling) clearInterval(this.polling);
                    this.polling = setInterval(() => {
                        this.loadChats(false);
                    }, 3000);
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const el = this.$refs.chatContainer;
                        if (el) {
                            el.scrollTop = el.scrollHeight;
                        }
                    });
                },

                // 1. Buka Modal
                confirmClearChat() {
                    if (!this.activeUser) return;
                    this.showClearModal = true; // Munculkan modal
                },

                performClearChat() {
                    const userIdToDelete = this.activeUser.id; // Simpan ID dulu

                    // 1. Update UI: Hapus chat, tutup modal, hapus dari sidebar
                    this.chats = [];
                    this.showClearModal = false;
                    this.conversations = this.conversations.filter(u => u.id !== userIdToDelete);
                    this.activeUser = null; // Kembali ke layar depan

                    // 2. Request ke Server
                    const token = document.querySelector('meta[name="csrf-token"]').content;

                    fetch(`{{ url('admin/chat/clear') }}/${userIdToDelete}`, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": token
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal menghapus');
                            console.log('Percakapan dihapus dari server.');
                        })
                        .catch(err => {
                            console.error(err);
                            // Opsional: Jika gagal, mungkin perlu reload halaman
                            // location.reload(); 
                        });
                },
            }
        }
    </script>
</body>

</html>
