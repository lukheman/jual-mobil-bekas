<?php

use App\Enums\ClickType;
use App\Enums\LeadStatus;
use App\Models\Car;
use App\Models\ClickLog;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('the landing page renders with its styles', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Temukan mobil yang')
        ->assertSee('Cari mobil impianmu')
        ->assertSee('MobilTa')
        ->assertSee('Login')
        ->assertSee(route('login'), false);
});

test('guests can view the login and register pages', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Masuk ke panel admin RodaTa')
        ->assertSee('function togglePassword', false);

    $this->get('/register')
        ->assertOk()
        ->assertSee('Create Account')
        ->assertSee('function togglePassword', false);
});

test('admin pages require authentication', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/admin/users')->assertRedirect('/login');
    $this->get('/admin/profile')->assertRedirect('/login');
    $this->get('/admin/components')->assertRedirect('/login');
});

test('authenticated users can view the admin pages', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)->get('/admin/dashboard')->assertOk()->assertSee('Dashboard Overview');
    $this->actingAs($user)->get('/admin/users')->assertOk()->assertSee('User Management');
    $this->actingAs($user)->get('/admin/profile')
        ->assertOk()
        ->assertSee('Informasi Profile')
        ->assertSee('Keamanan Akun')
        ->assertSee('Foto Profil')
        ->assertSee('function togglePasswordSection', false);
    $this->actingAs($user)->get('/admin/components')->assertOk()->assertSee('Component Library');
    $this->actingAs($user)->get('/admin/components?section=media')
        ->assertOk()
        ->assertSee('Components for displaying and uploading media.');

    $this->actingAs($user)->get('/admin/users?create=1')->assertOk()->assertSee('Create New User');
    $this->actingAs($user)->get('/admin/users?edit='.$otherUser->id)->assertOk()->assertSee('Edit User');
    $this->actingAs($user)->get('/admin/users?delete='.$otherUser->id)->assertOk()->assertSee('Confirm Delete');

    $this->actingAs($user)->get('/admin/users')
        ->assertOk()
        ->assertSee('href="'.route('admin.users', ['edit' => $otherUser->id]).'"', false)
        ->assertSee('href="'.route('admin.users', ['delete' => $otherUser->id]).'"', false);
});

test('the dashboard summarizes inventory leads and engagement', function () {
    $user = User::factory()->create();

    $car = Car::factory()->available()->create([
        'price' => 200_000_000,
        'views_count' => 120,
        'city' => 'Balikpapan',
    ]);
    Car::factory()->sold()->create(['views_count' => 30]);

    Lead::factory()->create([
        'car_id' => $car->id,
        'name' => 'Rina Pembeli',
        'status' => LeadStatus::New,
    ]);
    Lead::factory()->deal()->create([
        'car_id' => $car->id,
        'name' => 'Budi Deal',
    ]);

    ClickLog::create([
        'car_id' => $car->id,
        'type' => ClickType::Whatsapp,
    ]);

    $this->actingAs($user)->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Dashboard Overview')
        ->assertSee('Status Inventaris')
        ->assertSee('Lead Terbaru')
        ->assertSee('Mobil Paling Dilihat')
        ->assertSee('Rina Pembeli')
        ->assertSee('Budi Deal')
        ->assertSee('Pipeline Lead')
        ->assertSee('Sebaran Merek')
        ->assertSee('Perlu Tindakan');
});

test('users can log in', function () {
    $user = User::factory()->create(['password' => 'secret123']);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertRedirect('/admin/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('users can register', function () {
    $this->post('/register', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'agree_terms' => '1',
    ])->assertRedirect('/admin/dashboard');

    $this->assertAuthenticated();
    expect(User::where('email', 'new@example.com')->exists())->toBeTrue();
});

test('users can be created updated and deleted', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/admin/users');

    $user = User::where('email', 'new@example.com')->firstOrFail();

    $this->actingAs($admin)->put("/admin/users/{$user->id}", [
        'name' => 'Updated User',
        'email' => 'updated@example.com',
    ])->assertRedirect('/admin/users');

    expect($user->fresh()->name)->toBe('Updated User');

    $this->actingAs($admin)->delete("/admin/users/{$user->id}")->assertRedirect('/admin/users');

    expect(User::find($user->id))->toBeNull();
});

test('invalid user data reopens the create modal', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->from('/admin/users?create=1')
        ->post('/admin/users', [])
        ->assertRedirect('/admin/users?create=1')
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('the profile and password can be updated', function () {
    $user = User::factory()->create(['password' => 'password']);

    $this->actingAs($user)->put('/admin/profile', [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ])->assertRedirect();

    expect($user->fresh()->name)->toBe('Updated Name');

    $this->actingAs($user)->put('/admin/profile/password', [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect();

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('the avatar can be uploaded and removed', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)->post('/admin/profile/avatar', [
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertRedirect();

    $path = $user->fresh()->avatar;

    expect($path)->not->toBeNull();
    Storage::disk('public')->assertExists($path);

    $this->actingAs($user)->delete('/admin/profile/avatar')->assertRedirect();

    expect($user->fresh()->avatar)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
