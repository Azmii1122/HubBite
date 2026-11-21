<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - HubBite Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex min-h-screen font-sans">

    <!-- Sidebar Admin -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30">
        <div>
            <div class="p-6 border-b border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-extrabold text-base shadow-sm shadow-orange-500/30">
                        HB
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-orange-500 tracking-tight leading-none">
                            HubBite <span class="text-gray-900">Admin</span>
                        </h2>
                        <p class="text-[11px] text-gray-400 mt-1">Central Management Control</p>
                    </div>
                </a>
            </div>

            <div class="px-4 pt-5 pb-2">
                <p class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Menu Utama</p>
            </div>

            <nav class="space-y-1 px-3">
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.users.*') ? 'font-bold text-orange-600 bg-orange-50/80 border border-orange-200/70' : 'font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-orange-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Manajemen Pengguna</span>
                    </span>
                    @if(request()->routeIs('admin.users.*'))
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                    @endif
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.orders.*') ? 'font-bold text-orange-600 bg-orange-50/80 border border-orange-200/70' : 'font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.orders.*') ? 'text-orange-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Semua Pesanan</span>
                    </span>
                    @if(request()->routeIs('admin.orders.*'))
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                    @endif
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Profil Akun (Breeze)</span>
                </a>
            </nav>
        </div>

        <!-- Logged-in Breeze User Footer -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-extrabold text-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-orange-600 mt-0.5 uppercase">
                        ● Role: {{ Auth::user()->role }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar (Logout)
                </button>
            </form>
        </div>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 ml-64 p-8">
        @if(session('success'))
            <div id="flash-success" class="mb-6 flex items-center justify-between gap-4 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-3.5 text-emerald-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('flash-success').remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold">
                    Tutup
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="flash-error" class="mb-6 flex items-center justify-between gap-4 rounded-2xl bg-red-50 border border-red-200 px-5 py-3.5 text-red-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center text-xs font-bold shrink-0">!</span>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('flash-error').remove()" class="text-red-600 hover:text-red-800 text-xs font-bold">
                    Tutup
                </button>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
