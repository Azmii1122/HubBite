<x-app-layout>
    <x-slot name="title">Masuk ke HubBite</x-slot>

    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 border border-gray-100 shadow-xl space-y-6">
            
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-500 text-white flex items-center justify-center mx-auto mb-4 text-2xl shadow-lg shadow-brand-500/30">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-xs text-gray-400 mt-1">Masuk untuk mulai pesan makanan favoritmu</p>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-2xl text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Akun</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', 'budi@hubbite.com') }}" 
                           required 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <input type="password" 
                           name="password" 
                           value="password"
                           required 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                        <input type="checkbox" name="remember" checked class="rounded text-brand-500 focus:ring-brand-500">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="text-brand-600 hover:text-brand-700 font-bold">Lupa sandi?</a>
                </div>

                <button type="submit" 
                        class="w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-3.5 px-4 rounded-2xl text-sm transition-all shadow-lg shadow-brand-500/30">
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="text-center pt-4 border-t border-gray-100 text-xs text-gray-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700">Daftar sekarang</a>
            </div>

        </div>
    </div>
</x-app-layout>
