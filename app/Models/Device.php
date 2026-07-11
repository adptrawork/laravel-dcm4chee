<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'server_id', 'name', 'ae_title', 'hostname', 'port', 'modality', 'status',
        'last_echo_at', 'last_mwl_query_at', 'last_store_at', 'queue_count',
    ];

    protected function casts(): array
    {
        return [
            'last_echo_at' => 'datetime',
            'last_mwl_query_at' => 'datetime',
            'last_store_at' => 'datetime',
            'queue_count' => 'integer',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
