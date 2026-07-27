<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immunization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'recommended_age',
        'age_in_months',
        'description',
    ];

    public static function getStaticOptions(): array
    {
        return [
            ['id' => 1, 'name' => 'Hepatitis B (HB-0)', 'code' => 'HB0', 'category' => 'Wajib Dasar', 'recommended_age' => '0 Bulan (24 jam)', 'age_in_months' => 0, 'description' => 'Mencegah penularan Hepatitis B dari ibu ke bayi.'],
            ['id' => 2, 'name' => 'BCG', 'code' => 'BCG', 'category' => 'Wajib Dasar', 'recommended_age' => '1 Bulan', 'age_in_months' => 1, 'description' => 'Mencegah penyakit TBC (Tuberculosis) berat.'],
            ['id' => 3, 'name' => 'Polio Tetes 1 (BOPV 1)', 'code' => 'BOPV 1', 'category' => 'Wajib Dasar', 'recommended_age' => '1 Bulan', 'age_in_months' => 1, 'description' => 'Mencegah kelumpuhan akibat virus Polio.'],
            ['id' => 4, 'name' => 'DPT-HB-Hib 1', 'code' => 'DPT-HB-Hib 1', 'category' => 'Wajib Dasar', 'recommended_age' => '2 Bulan', 'age_in_months' => 2, 'description' => 'Mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan Hib.'],
            ['id' => 5, 'name' => 'Polio Tetes 2 (BOPV 2)', 'code' => 'BOPV 2', 'category' => 'Wajib Dasar', 'recommended_age' => '2 Bulan', 'age_in_months' => 2, 'description' => 'Dosis kedua imunisasi Polio tetes.'],
            ['id' => 6, 'name' => 'DPT-HB-Hib 2', 'code' => 'DPT-HB-Hib 2', 'category' => 'Wajib Dasar', 'recommended_age' => '3 Bulan', 'age_in_months' => 3, 'description' => 'Dosis kedua DPT-HB-Hib.'],
            ['id' => 7, 'name' => 'Polio Tetes 3 (BOPV 3)', 'code' => 'BOPV 3', 'category' => 'Wajib Dasar', 'recommended_age' => '3 Bulan', 'age_in_months' => 3, 'description' => 'Dosis ketiga imunisasi Polio tetes.'],
            ['id' => 8, 'name' => 'DPT-HB-Hib 3', 'code' => 'DPT-HB-Hib 3', 'category' => 'Wajib Dasar', 'recommended_age' => '4 Bulan', 'age_in_months' => 4, 'description' => 'Dosis ketiga DPT-HB-Hib.'],
            ['id' => 9, 'name' => 'Polio Tetes 4 (BOPV 4)', 'code' => 'BOPV 4', 'category' => 'Wajib Dasar', 'recommended_age' => '4 Bulan', 'age_in_months' => 4, 'description' => 'Dosis keempat imunisasi Polio tetes.'],
            ['id' => 10, 'name' => 'Polio Suntik 1 (IPV 1)', 'code' => 'IPV 1', 'category' => 'Wajib Dasar', 'recommended_age' => '4 Bulan', 'age_in_months' => 4, 'description' => 'Imunisasi Polio suntik dosis pertama.'],
            ['id' => 11, 'name' => 'Campak Rubella (MR 1)', 'code' => 'MR 1', 'category' => 'Wajib Dasar', 'recommended_age' => '9 Bulan', 'age_in_months' => 9, 'description' => 'Mencegah penyakit Campak dan Rubella.'],
            ['id' => 12, 'name' => 'Polio Suntik 2 (IPV 2)', 'code' => 'IPV 2', 'category' => 'Wajib Dasar', 'recommended_age' => '9 Bulan', 'age_in_months' => 9, 'description' => 'Imunisasi Polio suntik dosis kedua.'],
            ['id' => 13, 'name' => 'DPT-HB-Hib Lanjutan (Booster)', 'code' => 'DPT Booster', 'category' => 'Lanjutan', 'recommended_age' => '18 Bulan', 'age_in_months' => 18, 'description' => 'Penguat kekebalan DPT-HB-Hib.'],
            ['id' => 14, 'name' => 'Campak Rubella Lanjutan (MR 2)', 'code' => 'MR 2', 'category' => 'Lanjutan', 'recommended_age' => '18 Bulan', 'age_in_months' => 18, 'description' => 'Penguat kekebalan Campak Rubella.'],
        ];
    }

    public static function getAllOrStatic()
    {
        $items = static::orderBy('age_in_months')->get();
        if ($items->isEmpty()) {
            // Auto seed if empty so DB is populated
            foreach (static::getStaticOptions() as $option) {
                static::updateOrCreate(['code' => $option['code']], $option);
            }

            return static::orderBy('age_in_months')->get();
        }

        return $items;
    }
}
