<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HubBite Seller' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-text-primary min-h-screen antialiased">
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[520px]">
            {{-- Left Panel - Orange Gradient --}}
            <div class="md:w-5/12 bg-gradient-to-br from-hubbite to-orange-400 p-8 md:p-10 flex flex-col justify-center text-white relative overflow-hidden md:rounded-l-3xl">
                {{-- Decorative circles --}}
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-white/5 rounded-full"></div>

                {{-- Logo --}}
                <div class="flex items-center gap-2 mb-8 relative z-10">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-hubbite" fill="currentColor" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                    </div>
                    <span class="text-xl font-bold">Hub<span class="font-extrabold">Bite</span></span>
                </div>

                {{-- Slogan --}}
                <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-6 relative z-10">
                    Pesan Makanan Cepat,<br>Bebas Antre.
                </h2>

                {{-- Description --}}
                <p class="text-white/80 text-sm leading-relaxed relative z-10">
                    HubBite menghubungkanmu langsung dengan warung favorit di sekitar Telkom University.
                    Pantau keramaian secara real-time, pesan lewat sistem pre-order, dan nikmati waktu istirahat
                    kelasmu tanpa terbuang sia-sia.
                </p>
            </div>

            {{-- Right Panel - Form --}}
            <div class="md:w-7/12 p-8 md:p-10 flex flex-col justify-center">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="fixed top-4 right-4 z-[60] space-y-2" x-data>
        <template x-for="toast in $store.toast.items" :key="toast.id">
            <div class="toast-enter flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg min-w-[280px] max-w-sm"
                 :class="{
                     'bg-green-50 border border-green-200 text-green-800': toast.type === 'success',
                     'bg-red-50 border border-red-200 text-red-800': toast.type === 'error',
                 }">
                <span x-text="toast.message" class="text-sm font-medium"></span>
            </div>
        </template>
    </div>

    @if(session('success'))
        <script>document.addEventListener('alpine:init', () => Alpine.store('toast').success('{{ session('success') }}'));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('alpine:init', () => Alpine.store('toast').error('{{ session('error') }}'));</script>
    @endif
</body>
</html>

