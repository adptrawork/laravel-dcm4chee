<?php

namespace App\Models;

use App\Jobs\PushWorklistToPacsJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REPORTED = 'reported';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SCHEDULED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_REPORTED,
        self::STATUS_CANCELLED,
    ];

    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_SCHEDULED => 'Scheduled',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_REPORTED => 'Reported',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING => 'gray',
        self::STATUS_SCHEDULED => 'warning',
        self::STATUS_IN_PROGRESS => 'info',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_REPORTED => 'purple',
        self::STATUS_CANCELLED => 'danger',
    ];

    protected $fillable = [
        'patient_id', 'accession_number', 'procedure_id', 'device_id', 'modality',
        'requesting_physician', 'clinical_notes', 'status', 'priority',
        'scheduled_date',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->accession_number)) {
                $order->accession_number = static::generateAccessionNumber();
            }
        });

        static::created(function (Order $order) {
            $status = $order->status ?? Order::STATUS_PENDING;
            if ($status === Order::STATUS_PENDING) {
                PushWorklistToPacsJob::dispatch($order);
            }
        });
    }

    public static function generateAccessionNumber(): string
    {
        $prefix = 'ACC-' . now()->format('Ymd') . '-';
        $last = static::where('accession_number', 'like', $prefix . '%')
            ->orderBy('accession_number', 'desc')
            ->value('accession_number');

        $sequence = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function worklistItem()
    {
        return $this->hasOne(WorklistItem::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }
}
