<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HubBite Seller' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#F8F9FC] font-sans text-text-primary min-h-screen antialiased">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false, showLogoutModal: false }">
        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/40 z-40 lg:hidden" x-cloak></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-border flex flex-col transition-transform duration-200 lg:translate-x-0">
            {{-- Logo --}}
            <div class="px-5 pt-6 pb-4">
                <h1 class="text-xl font-extrabold text-hubbite">HubBite <span class="text-text-primary">Seller</span></h1>
                <p class="text-xs text-text-secondary mt-0.5">{{ auth()->user()->store?->campus_hub ?? 'Campus Hub - Central' }}</p>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 space-y-1">
                @php
                    $navItems = [
                        ['route' => 'seller.dashboard', 'active' => 'seller.dashboard', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>', 'label' => 'Dashboard'],
                        ['route' => 'seller.menus.index', 'active' => 'seller.menus.*', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>', 'label' => 'Menu Management'],
                        ['route' => 'seller.orders.index', 'active' => 'seller.orders.*', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>', 'label' => 'Order History'],
                        ['route' => 'seller.analytics.index', 'active' => 'seller.analytics.*', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>', 'label' => 'Analytics'],
                        ['route' => 'seller.settings.index', 'active' => 'seller.settings.*', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'label' => 'Settings'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['active']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $isActive ? 'text-hubbite bg-hubbite-light border-l-4 border-hubbite' : 'text-text-secondary hover:text-text-primary hover:bg-gray-50' }}">
                        {!! $item['icon'] !!}
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Bottom Section --}}
            <div class="mt-auto px-4 pb-4 space-y-3">
                <div class="flex items-center gap-3 py-3 border-t border-border">
                    <div class="w-10 h-10 rounded-full bg-hubbite-light flex items-center justify-center overflow-hidden shrink-0">
                        @if(auth()->user()->store?->avatar)
                            <img src="{{ auth()->user()->store->avatar }}" class="w-full h-full object-cover" alt="Avatar">
                        @else
                            <span class="text-hubbite font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-text-primary truncate">{{ auth()->user()->store?->store_name ?? auth()->user()->name }}</p>
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-xs text-text-secondary">Verified Seller</span>
                        </div>
                    </div>
                </div>

                @php $store = auth()->user()->store; @endphp
                <form method="POST" action="{{ route('seller.store.goLive') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PATCH')
                    <button type="submit" :disabled="loading"
                            class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all {{ $store?->is_live ? 'bg-danger text-white hover:bg-red-600' : 'bg-hubbite text-white hover:bg-hubbite-dark' }}">
                        <span x-show="!loading">{{ $store?->is_live ? '⏸ Go Offline' : '🟢 Go Live' }}</span>
                        <span x-show="loading" x-cloak>Processing...</span>
                    </button>
                </form>

                <a href="{{ route('seller.help') }}" class="flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Help Center
                </a>

                <button @click="showLogoutModal = true" class="flex items-center gap-2 text-sm text-danger hover:text-red-700 transition-colors w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 min-w-0 overflow-auto">
            <div class="lg:hidden flex items-center justify-between p-4 bg-white border-b border-border">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-hubbite">HubBite <span class="text-text-primary">Seller</span></h1>
                <div class="w-8"></div>
            </div>

            @if(session('success'))
                <div class="mx-6 mt-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mx-6 mt-6 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="mx-6 mt-6 rounded-2xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm font-semibold text-amber-800">
                    {{ session('warning') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        {{-- Logout Modal --}}
        <div x-show="showLogoutModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
            <div x-show="showLogoutModal" x-transition.scale.90 @click.outside="showLogoutModal = false"
                 class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
                <div class="text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Konfirmasi Logout</h3>
                    <p class="text-sm text-text-secondary mb-6">Apakah kamu yakin ingin keluar dari HubBite Seller?</p>
                    <div class="flex gap-3">
                        <button @click="showLogoutModal = false" class="flex-1 px-4 py-2.5 border border-border rounded-xl text-sm font-medium text-text-primary hover:bg-gray-50 transition-colors">Batal</button>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 bg-danger text-white rounded-xl text-sm font-medium hover:bg-red-600 transition-colors">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast Container --}}
        <div class="fixed top-4 right-4 z-[60] space-y-2" x-data>
            <template x-for="toast in $store.toast.items" :key="toast.id">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg min-w-[280px] max-w-sm"
                     :class="{
                         'bg-green-50 border border-green-200 text-green-800': toast.type === 'success',
                         'bg-red-50 border border-red-200 text-red-800': toast.type === 'error',
                         'bg-yellow-50 border border-yellow-200 text-yellow-800': toast.type === 'warning',
                         'bg-blue-50 border border-blue-200 text-blue-800': toast.type === 'info',
                     }">
                    <span x-text="toast.message" class="text-sm font-medium"></span>
                </div>
            </template>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
