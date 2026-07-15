<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'worklist_item_id', 'order_id', 'study_instance_uid', 'accession_number',
        'radiologist_id', 'clinical_history', 'findings', 'impression',
        'conclusion', 'status', 'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (Report $r) {
            if ($r->status === 'final' && $r->order) {
                $from = $r->order->status;
                $r->order->updateQuietly(['status' => Order::STATUS_REPORTED]);
                Order::logStatus($r->order, Order::STATUS_REPORTED, 'Report finalized', $from);
                $r->worklistItem?->updateQuietly(['status' => WorklistItem::STATUS_REPORTED]);
            }
        });
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
