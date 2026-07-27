<?php

use App\Models\Immunization;
use App\Models\ImmunizationRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user can register and gets redirected to form page with auto generated unique code', function () {
    $response = $this->post('/register', [
        'name' => 'Bunda Dila',
        'email' => 'dila@gmail.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/form');
    $this->assertAuthenticated();

    $user = User::where('email', 'dila@gmail.com')->first();
    expect($user)->not->toBeNull();
    expect($user->unique_code)->toStartWith('SMN-');
    expect($user->role)->toBe('user');
});

test('user can login using email or phone number in single input', function () {
    $user = User::create([
        'unique_code' => 'SMN-099',
        'name' => 'Ibu Ana',
        'email' => 'ana@gmail.com',
        'phone' => '089876543210',
        'password' => Hash::make('secret123'),
        'role' => 'user',
    ]);

    // Test 1: Login using email
    $responseEmail = $this->post('/login', [
        'login_id' => 'ana@gmail.com',
        'password' => 'secret123',
    ]);
    $responseEmail->assertRedirect('/form');
    $this->assertAuthenticatedAs($user);

    // Logout
    $this->post('/logout');

    // Test 2: Login using phone number
    $responsePhone = $this->post('/login', [
        'login_id' => '089876543210',
        'password' => 'secret123',
    ]);
    $responsePhone->assertRedirect('/form');
    $this->assertAuthenticatedAs($user);
});

test('authenticated user can submit immunization form', function () {
    $user = User::factory()->create([
        'unique_code' => 'SMN-002',
        'phone' => '081234567890',
        'role' => 'user',
    ]);

    Immunization::create([
        'name' => 'BCG',
        'code' => 'BCG',
        'category' => 'Wajib',
        'recommended_age' => '1 Bulan',
        'age_in_months' => 1,
    ]);

    $response = $this->actingAs($user)->post('/form', [
        'child_name' => 'An. Brahim',
        'head_of_family' => $user->name,
        'father_job' => 'Wiraswasta',
        'mother_name' => 'Siti',
        'mother_job' => 'Guru',
        'gender' => 'laki-laki',
        'age_text' => '6 bulan',
        'birth_date' => '2026-01-10',
        'address' => 'Jl. Merdeka No. 10',
        'phone' => $user->phone,
        'email' => $user->email,
        'immunization_status' => 'tidak lengkap',
        'immunization_types' => ['BCG'],
        'incomplete_reason' => 'Anak sempat demam',
    ]);

    $response->assertRedirect('/confirmation');

    $record = ImmunizationRecord::where('user_id', $user->id)->first();
    expect($record)->not->toBeNull();
    expect($record->child_name)->toBe('An. Brahim');
    expect($record->immunization_status)->toBe('tidak lengkap');
});

test('admin can access admin dashboard and delete respondent record', function () {
    $admin = User::factory()->create([
        'unique_code' => 'SMN-ADM01',
        'role' => 'admin',
    ]);

    $user = User::factory()->create(['unique_code' => 'SMN-003']);
    $record = ImmunizationRecord::create([
        'user_id' => $user->id,
        'child_name' => 'An. Test',
        'head_of_family' => 'Ortu',
        'mother_name' => 'Ibu',
        'gender' => 'perempuan',
        'age_text' => '1 tahun',
        'birth_date' => '2025-05-05',
        'address' => 'Alamat',
        'phone' => '081234567890',
        'immunization_status' => 'lengkap',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');
    $response->assertOk();

    $deleteResponse = $this->actingAs($admin)->delete("/admin/respondents/{$record->id}");
    $deleteResponse->assertRedirect();
    expect(ImmunizationRecord::find($record->id))->toBeNull();
});
