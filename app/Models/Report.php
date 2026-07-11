<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Report extends Model
{
    use LogsActivity;
    protected $fillable = [
        'worklist_item_id', 'order_id', 'study_instance_uid', 'accession_number',
        'radiologist_id', 'clinical_history', 'findings', 'impression',
        'conclusion', 'status', 'finalized_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'clinical_history', 'findings', 'impression', 'conclusion'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function radiologist()
    {
        return $this->belongsTo(User::class, 'radiologist_id');
    }
}
