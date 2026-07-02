<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorklistItem extends Model
{
    protected $fillable = [
        'server_id', 'accession_number', 'patient_name', 'patient_id',
        'modality', 'procedure_description', 'requesting_physician',
        'scheduled_date', 'scheduled_time', 'status', 'study_uid',
        'error_message', 'sent_at',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
