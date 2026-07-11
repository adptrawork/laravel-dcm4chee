<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'worklist_item_id', 'study_instance_uid', 'accession_number',
        'radiologist_id', 'clinical_history', 'findings', 'impression',
        'conclusion', 'status', 'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    public function worklistItem()
    {
        return $this->belongsTo(WorklistItem::class);
    }

    public function radiologist()
    {
        return $this->belongsTo(User::class, 'radiologist_id');
    }
}
