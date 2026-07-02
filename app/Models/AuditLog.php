<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'server_id',
        'method',
        'endpoint',
        'url',
        'request_body',
        'response_body',
        'response_status',
        'duration_ms',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'duration_ms' => 'integer',
            'response_status' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
