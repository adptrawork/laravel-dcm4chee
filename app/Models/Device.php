<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'server_id', 'name', 'ae_title', 'hostname', 'port', 'modality', 'status',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
