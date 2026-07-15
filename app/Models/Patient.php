<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id', 'name', 'date_of_birth', 'sex',
        'phone', 'email', 'address', 'national_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Patient $patient) {
            if (empty($patient->patient_id)) {
                $count = static::whereDate('created_at', today())->count() + 1;
                $patient->patient_id = 'MRN-'.now()->format('Ymd').'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
