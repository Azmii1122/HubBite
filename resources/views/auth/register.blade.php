<x-app-layout>
    <x-slot name="title">Daftar Akun HubBite</x-slot>

    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 border border-gray-100 shadow-xl space-y-6">
            
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-500 text-white flex items-center justify-center mx-auto mb-4 text-2xl shadow-lg shadow-brand-500/30">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Buat Akun Baru</h1>
                <p class="text-xs text-gray-400 mt-1">Daftar sekarang untuk pesan makanan hemat di kampus</p>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-2xl text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Budi Sudarsono"
                           required 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Akun</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="nama@email.com"
                           required 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <input type="password" 
                           name="password" 
                           placeholder="Minimal 8 karakter"
                           required 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi</label>
                    <input type="password" 
                           name="password_confirmation" 
                           placeholder="Ulangi kata sandi"
                           required 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>

                <button type="submit" 
                        class="w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-3.5 px-4 rounded-2xl text-sm transition-all shadow-lg shadow-brand-500/30">
                    Daftar & Masuk ke Dashboard
                </button>
            </form>

            <div class="text-center pt-4 border-t border-gray-100 text-xs text-gray-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700">Masuk di sini</a>
            </div>

        </div>
    </div>
</x-app-layout>
