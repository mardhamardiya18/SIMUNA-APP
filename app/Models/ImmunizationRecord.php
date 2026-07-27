<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImmunizationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'child_name',
        'head_of_family',
        'father_job',
        'mother_name',
        'mother_job',
        'gender',
        'age_text',
        'birth_date',
        'address',
        'phone',
        'email',
        'immunization_status',
        'immunization_types',
        'incomplete_reason',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'immunization_types' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
