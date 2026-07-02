<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'name',
        'base_url',
        'archive',
        'aet',
        'username',
        'password',
        'timeout',
        'ssl_verify',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'ssl_verify' => 'boolean',
            'enabled' => 'boolean',
            'timeout' => 'integer',
        ];
    }

    public function getApiBaseUrlAttribute(): string
    {
        return rtrim($this->base_url, '/')
            . '/'
            . trim($this->archive, '/')
            . '/aets/'
            . trim($this->aet)
            . '/rs';
    }

    public function getKeycloakUrlAttribute(): string
    {
        return preg_replace('/:\d+$/', ':8843', rtrim($this->base_url, '/'));
    }
}
