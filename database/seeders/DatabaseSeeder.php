<?php

namespace Database\Seeders;

use App\Models\ImmunizationRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ImmunizationSeeder::class);

        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@simuna.go.id'],
            [
                'unique_code' => 'SMN-ADM01',
                'name' => 'Petugas Puskesmas SIMUNA',
                'email' => 'admin@simuna.go.id',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // Create Sample User (Bunda)
        $user = User::updateOrCreate(
            ['email' => 'bunda@gmail.com'],
            [
                'unique_code' => 'SMN-001',
                'name' => 'Bunda Dila',
                'email' => 'bunda@gmail.com',
                'phone' => '084261297408167',
                'role' => 'user',
                'password' => Hash::make('password'),
            ]
        );

        // Create Sample Immunization Record for Bunda Dila
        ImmunizationRecord::updateOrCreate(
            ['user_id' => $user->id],
            [
                'child_name' => 'An. X',
                'head_of_family' => 'Gilar',
                'father_job' => 'Bisnis',
                'mother_name' => 'Dila',
                'mother_job' => 'Dokter',
                'gender' => 'laki-laki',
                'age_text' => '1 tahun',
                'birth_date' => '2025-06-10',
                'address' => 'Jl. Kalimantan No. 12, Kota Sehat',
                'phone' => '084261297408167',
                'email' => 'bunda@gmail.com',
                'immunization_status' => 'tidak lengkap',
                'immunization_types' => ['HB0', 'BCG', 'BOPV 2'],
                'incomplete_reason' => 'Sibuk pekerjaan dan anak sempat demam ringan.',
            ]
        );
    }
}
