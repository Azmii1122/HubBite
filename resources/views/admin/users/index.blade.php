@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-orange-600 text-xs font-bold mb-2">
                <span>Panel Administrator</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna, atur hak akses role (Admin, Merchant, Buyer), serta cari data akun secara cepat.</p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-orange-500/20 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Pengguna Baru</span>
        </a>
    </div>

    <!-- Summary Role Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.users.index', ['role' => 'semua', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-5 border {{ $selectedRole === 'semua' ? 'border-orange-500 ring-2 ring-orange-500/10' : 'border-gray-200 hover:border-orange-300' }} shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Pengguna</span>
                <span class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-sm">All</span>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 mt-3">{{ $roleCounts['semua'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Seluruh akun terdaftar</p>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-5 border {{ $selectedRole === 'admin' ? 'border-purple-500 ring-2 ring-purple-500/10' : 'border-gray-200 hover:border-purple-300' }} shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Administrator</span>
                <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">AD</span>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 mt-3">{{ $roleCounts['admin'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Akses penuh sistem</p>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'merchant', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-5 border {{ $selectedRole === 'merchant' ? 'border-amber-500 ring-2 ring-amber-500/10' : 'border-gray-200 hover:border-amber-300' }} shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Merchant / Seller</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">MC</span>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 mt-3">{{ $roleCounts['merchant'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Pemilik warung / gerai</p>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'buyer', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-5 border {{ $selectedRole === 'buyer' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-gray-200 hover:border-emerald-300' }} shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Buyer / Mahasiswa</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">BY</span>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 mt-3">{{ $roleCounts['buyer'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Akun pembeli aktif</p>
        </a>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Search Input -->
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari berdasarkan username, email, atau ID user..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none transition">
            </div>

            <!-- Role Select & Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <select name="role"
                        onchange="this.form.submit()"
                        class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="semua" {{ $selectedRole === 'semua' ? 'selected' : '' }}>
                        Semua Role ({{ $roleCounts['semua'] ?? 0 }})
                    </option>
                    @foreach($roles as $roleKey => $roleLabel)
                        <option value="{{ $roleKey }}" {{ $selectedRole === $roleKey ? 'selected' : '' }}>
                            {{ $roleLabel }} ({{ $roleCounts[$roleKey] ?? 0 }})
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition">
                    Cari
                </button>

                @if($search !== '' || $selectedRole !== 'semua')
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID User</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi CRUD</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $item)
                        <tr class="hover:bg-orange-50/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-500">
                                #{{ $item->user_id }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-extrabold text-xs shrink-0">
                                        {{ strtoupper(substr($item->username, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900">{{ $item->username }}</span>
                                            @if(Auth::user()->user_id === $item->user_id)
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-orange-100 text-orange-700">
                                                    Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $item->email }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeStyle = match(strtolower($item->role)) {
                                        'admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'merchant' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        default => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border uppercase {{ $badgeStyle }}">
                                    {{ $item->role }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.users.show', $item->user_id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                                        Detail
                                    </a>

                                    <a href="{{ route('admin.users.edit', $item->user_id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold transition">
                                        Edit
                                    </a>

                                    @if(Auth::user()->user_id !== $item->user_id)
                                        <button type="button"
                                                onclick="openDeleteModal({{ $item->user_id }}, @js($item->username), @js(route('admin.users.destroy', $item->user_id)))"
                                                class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold transition">
                                            Hapus
                                        </button>
                                    @else
                                        <span class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-300 font-bold cursor-not-allowed" title="Tidak dapat menghapus akun yang sedang login">
                                            Hapus
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center mx-auto mb-3 font-extrabold">
                                        ?
                                    </div>
                                    <p class="text-sm font-bold text-gray-800">Tidak ada pengguna ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian atau filter role yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteUserModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-gray-100">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-extrabold text-lg mb-4">
                !
            </div>
            <h3 class="text-lg font-extrabold text-gray-900">Hapus Pengguna?</h3>
            <p class="text-sm text-gray-500 mt-1">
                Apakah Anda yakin ingin menghapus akun <span id="deleteUserName" class="font-bold text-gray-900"></span>? Tindakan ini tidak dapat dibatalkan.
            </p>

            <form id="deleteUserForm" method="POST" class="mt-6 flex items-center justify-end gap-2.5">
                @csrf
                @method('DELETE')
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-sm transition">
                    Ya, Hapus Pengguna
                </button>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(userId, username, actionUrl) {
            document.getElementById('deleteUserName').textContent = username + ' (#' + userId + ')';
            document.getElementById('deleteUserForm').setAttribute('action', actionUrl);
            const modal = document.getElementById('deleteUserModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteUserModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection
