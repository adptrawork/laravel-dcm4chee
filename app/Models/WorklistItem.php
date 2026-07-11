<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorklistItem extends Model
{
    const STATUS_REGISTERED = 'registered';
    const STATUS_MW_PUBLISHED = 'mw_published';
    const STATUS_TAKEN_BY_MODALITY = 'taken_by_modality';
    const STATUS_ACQUIRING = 'acquiring';
    const STATUS_ACQUIRED = 'acquired';
    const STATUS_SENT_TO_PACS = 'sent_to_pacs';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_REPORTED = 'reported';
    const STATUS_VERIFIED = 'verified';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    const STATUSES = [
        self::STATUS_REGISTERED,
        self::STATUS_MW_PUBLISHED,
        self::STATUS_TAKEN_BY_MODALITY,
        self::STATUS_ACQUIRING,
        self::STATUS_ACQUIRED,
        self::STATUS_SENT_TO_PACS,
        self::STATUS_ARCHIVED,
        self::STATUS_REPORTED,
        self::STATUS_VERIFIED,
        self::STATUS_CANCELLED,
        self::STATUS_FAILED,
    ];

    const STATUS_LABELS = [
        self::STATUS_REGISTERED => 'Registered',
        self::STATUS_MW_PUBLISHED => 'MWL Published',
        self::STATUS_TAKEN_BY_MODALITY => 'Taken by Modality',
        self::STATUS_ACQUIRING => 'Acquiring',
        self::STATUS_ACQUIRED => 'Acquired',
        self::STATUS_SENT_TO_PACS => 'Sent to PACS',
        self::STATUS_ARCHIVED => 'Archived',
        self::STATUS_REPORTED => 'Reported',
        self::STATUS_VERIFIED => 'Verified',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_FAILED => 'Failed',
    ];

    const STATUS_COLORS = [
        self::STATUS_REGISTERED => 'bg-gray-100 text-gray-700',
        self::STATUS_MW_PUBLISHED => 'bg-blue-100 text-blue-700',
        self::STATUS_TAKEN_BY_MODALITY => 'bg-indigo-100 text-indigo-700',
        self::STATUS_ACQUIRING => 'bg-yellow-100 text-yellow-700',
        self::STATUS_ACQUIRED => 'bg-orange-100 text-orange-700',
        self::STATUS_SENT_TO_PACS => 'bg-purple-100 text-purple-700',
        self::STATUS_ARCHIVED => 'bg-green-100 text-green-700',
        self::STATUS_REPORTED => 'bg-teal-100 text-teal-700',
        self::STATUS_VERIFIED => 'bg-emerald-100 text-emerald-700',
        self::STATUS_CANCELLED => 'bg-red-100 text-red-700',
        self::STATUS_FAILED => 'bg-red-100 text-red-700',
    ];

    protected $fillable = [
        'server_id', 'accession_number', 'patient_name', 'patient_id',
        'modality', 'procedure_code', 'procedure_description',
        'requested_procedure_id', 'sps_id', 'requesting_physician',
        'scheduled_date', 'scheduled_time', 'status', 'study_instance_uid',
        'error_message', 'sent_at',
        'taken_at', 'acquired_at', 'archived_at',
        'reported_at', 'verified_at', 'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'taken_at' => 'datetime',
            'acquired_at' => 'datetime',
            'archived_at' => 'datetime',
            'reported_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByServer($query, $serverId)
    {
        return $query->where('server_id', $serverId);
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ucfirst($status);
    }

    public static function statusColor(string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'bg-gray-100 text-gray-700';
    }
}
