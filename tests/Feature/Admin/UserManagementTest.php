<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('non-admin users cannot access admin user management pages', function () {
    $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

    $this->actingAs($buyer)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('guests are redirected to breeze login when accessing admin user management', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});

test('admin can list, search, and filter users by role', function () {
    $admin = User::factory()->admin()->create([
        'username' => 'Super Admin',
        'email' => 'admin@hubbite.com',
    ]);
    User::factory()->merchant()->create([
        'username' => 'Dafka Kitchen',
        'email' => 'dafka@hubbite.com',
    ]);
    User::factory()->create([
        'username' => 'Budi Mahasiswa',
        'email' => 'budi@hubbite.com',
        'role' => User::ROLE_BUYER,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => 'Dafka', 'role' => 'merchant']))
        ->assertOk()
        ->assertSee('Dafka Kitchen')
        ->assertDontSee('Budi Mahasiswa');
});

test('admin can create a new user with role', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'username' => 'Merchant Baru',
        'email' => 'merchantbaru@hubbite.com',
        'role' => User::ROLE_MERCHANT,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'username' => 'Merchant Baru',
        'email' => 'merchantbaru@hubbite.com',
        'role' => User::ROLE_MERCHANT,
    ]);
});

test('admin can update an existing user and prevent self role demotion', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->create([
        'username' => 'User Lama',
        'email' => 'lama@hubbite.com',
        'role' => User::ROLE_BUYER,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $targetUser->user_id), [
            'username' => 'User Diperbarui',
            'email' => 'baru@hubbite.com',
            'role' => User::ROLE_MERCHANT,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'user_id' => $targetUser->user_id,
        'username' => 'User Diperbarui',
        'email' => 'baru@hubbite.com',
        'role' => User::ROLE_MERCHANT,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin->user_id), [
            'username' => $admin->username,
            'email' => $admin->email,
            'role' => User::ROLE_BUYER,
        ])
        ->assertSessionHas('error');

    expect($admin->fresh()->role)->toBe(User::ROLE_ADMIN);
});

test('admin can delete another user but cannot delete their own active account', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $targetUser->user_id))
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', [
        'user_id' => $targetUser->user_id,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin->user_id))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
        'user_id' => $admin->user_id,
    ]);
});
