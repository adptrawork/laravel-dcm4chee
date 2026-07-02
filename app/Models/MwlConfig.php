<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MwlConfig extends Model
{
    protected $fillable = [
        'server_id', 'default_aet', 'default_modality',
        'default_physician', 'default_room', 'default_institution',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
