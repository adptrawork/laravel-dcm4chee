<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationTemplate extends Model
{
    protected $fillable = [
        'name', 'modality', 'description', 'room', 'priority', 'sort_order',
    ];
}
