@extends('layouts.admin')

@section('title', 'Detail Pengguna - ' . $user->username)

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Back Link & Header -->
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-600 transition mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Daftar Pengguna</span>
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Detail Informasi Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Rincian profil akun dan hak akses role pada sistem HubBite.</p>
        </div>

        <!-- Profile Card -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 sm:px-8 py-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-xs border border-white/30 flex items-center justify-center font-extrabold text-2xl">
                        {{ strtoupper(substr($user->username, 0, 2)) }}
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-100">ID Pengguna #{{ $user->user_id }}</span>
                        <h2 class="text-xl font-extrabold">{{ $user->username }}</h2>
                        <p class="text-xs text-orange-50 mt-0.5">{{ $user->email }}</p>
                    </div>
                </div>

                <div>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-extrabold uppercase bg-white text-orange-600 shadow-sm">
                        {{ $roles[$user->role] ?? strtoupper($user->role) }}
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Username</span>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $user->username }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Alamat Email</span>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $user->email }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Role Sistem</span>
                        <p class="text-sm font-bold text-gray-900 mt-1 uppercase">
                            {{ $user->role }} ({{ $roles[$user->role] ?? $user->role }})
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Status Sesi</span>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            @if(Auth::user()->user_id === $user->user_id)
                                <span class="text-emerald-600">● Sedang Login (Akun Anda)</span>
                            @else
                                <span class="text-gray-600">Terdaftar Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition">
                        Kembali
                    </a>

                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.users.edit', $user->user_id) }}"
                           class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold shadow-sm transition">
                            Edit Pengguna
                        </a>

                        @if(Auth::user()->user_id !== $user->user_id)
                            <form method="POST"
                                  action="{{ route('admin.users.destroy', $user->user_id) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-bold transition">
                                    Hapus Pengguna
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
