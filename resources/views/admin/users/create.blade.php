@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

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
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Tambah Pengguna Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Buat akun pengguna baru berdasarkan role yang tersedia pada sistem HubBite.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Username / Nama Lengkap *
                        </label>
                        <input type="text"
                               id="username"
                               name="username"
                               value="{{ old('username') }}"
                               required
                               placeholder="Contoh: Andi Pratama"
                               class="w-full px-4 py-2.5 bg-gray-50 border @error('username') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        @error('username')
                            <p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Alamat Email *
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="nama@hubbite.com"
                               class="w-full px-4 py-2.5 bg-gray-50 border @error('email') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        @error('email')
                            <p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pilihan Role -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2.5">
                        Pilih Role Pengguna *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($roles as $roleKey => $roleLabel)
                            @php
                                $roleDesc = match($roleKey) {
                                    'admin' => 'Akses penuh mengelola pengguna & seluruh sistem.',
                                    'merchant' => 'Mengelola menu warung dan memproses pesanan.',
                                    default => 'Memesan makanan dan melihat riwayat transaksi.',
                                };
                            @endphp
                            <label class="relative flex flex-col justify-between p-4 rounded-2xl border border-gray-200 hover:border-orange-400 cursor-pointer transition bg-gray-50/50 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50/60 has-[:checked]:ring-2 has-[:checked]:ring-orange-500/15">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-extrabold text-gray-900 uppercase">{{ $roleKey }}</span>
                                    <input type="radio"
                                           name="role"
                                           value="{{ $roleKey }}"
                                           {{ old('role', 'buyer') === $roleKey ? 'checked' : '' }}
                                           class="text-orange-500 focus:ring-orange-500">
                                </div>
                                <p class="text-xs font-semibold text-gray-700">{{ $roleLabel }}</p>
                                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">{{ $roleDesc }}</p>
                            </label>
                        @endforeach
                    </div>
                    @error('role')
                        <p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-gray-100">
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Kata Sandi *
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               placeholder="Minimal 6 karakter"
                               class="w-full px-4 py-2.5 bg-gray-50 border @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        @error('password')
                            <p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Konfirmasi Kata Sandi *
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               placeholder="Ulangi kata sandi"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold shadow-sm shadow-orange-500/20 transition">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
