<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Patient extends Model
{
    use SoftDeletes, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['patient_id', 'name', 'date_of_birth', 'sex', 'phone'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Patient $patient) {
            if (empty($patient->patient_id)) {
                $count = static::whereDate('created_at', today())->count() + 1;
                $patient->patient_id = 'MRN-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


}
