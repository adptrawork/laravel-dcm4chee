<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'modality', 'body_part',
        'estimated_duration', 'default_physician', 'default_room',
        'default_exposure', 'requires_contrast', 'contrast_detail',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_contrast' => 'boolean',
            'is_active' => 'boolean',
            'estimated_duration' => 'integer',
            'sort_order' => 'integer',
        ];
    }
}
