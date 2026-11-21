<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Available user roles in HubBite.
     *
     * @return array<string, string>
     */
    private function availableRoles(): array
    {
        return [
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_MERCHANT => 'Merchant / Seller',
            User::ROLE_BUYER => 'Buyer / Mahasiswa',
        ];
    }

    /**
     * Display a listing of users with search and role filter.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedRole = strtolower((string) $request->query('role', 'semua'));
        $roles = $this->availableRoles();

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('user_id', (int) $search);
                }
            });
        }

        if ($selectedRole !== '' && $selectedRole !== 'semua' && array_key_exists($selectedRole, $roles)) {
            $query->where('role', $selectedRole);
        } else {
            $selectedRole = 'semua';
        }

        $users = $query->orderByDesc('user_id')->paginate(10)->withQueryString();

        $roleCounts = [
            'semua' => User::count(),
            User::ROLE_ADMIN => User::where('role', User::ROLE_ADMIN)->count(),
            User::ROLE_MERCHANT => User::where('role', User::ROLE_MERCHANT)->count(),
            User::ROLE_BUYER => User::where('role', User::ROLE_BUYER)->count(),
        ];

        return view('admin.users.index', compact(
            'users',
            'roles',
            'roleCounts',
            'search',
            'selectedRole'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = $this->availableRoles();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna '{$user->username}' ({$user->role}) berhasil ditambahkan.");
    }

    /**
     * Display the specified user detail.
     */
    public function show(User $user): View
    {
        $roles = $this->availableRoles();

        return view('admin.users.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = $this->availableRoles();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->user()->user_id === $user->user_id && $validated['role'] !== User::ROLE_ADMIN) {
            return back()
                ->withInput()
                ->with('error', 'Anda tidak dapat menurunkan role akun Admin yang sedang Anda gunakan sendiri.');
        }

        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Data pengguna '{$user->username}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->user_id === $user->user_id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan untuk login.');
        }

        $deletedUsername = $user->username;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna '{$deletedUsername}' berhasil dihapus dari sistem.");
    }
}
