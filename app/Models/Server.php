<?php

namespace App\Models;

use App\Services\Dcm4chee\AuthService;
use App\Services\Dcm4chee\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Server extends Model
{
    protected $fillable = [
        'name',
        'base_url',
        'archive',
        'aet',
        'keycloak_url',
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
            .'/'
            .trim($this->archive, '/')
            .'/aets/'
            .trim($this->aet)
            .'/rs';
    }

    public function getWadoUriAttribute(): string
    {
        return rtrim($this->base_url, '/')
            .'/'
            .trim($this->archive, '/')
            .'/aets/'
            .trim($this->aet)
            .'/wado';
    }

    public function getKeycloakUrlAttribute(): string
    {
        if ($k = $this->attributes['keycloak_url'] ?? null) {
            return $k;
        }

        $url = preg_replace('/:\d+$/', ':8843', rtrim($this->base_url, '/'));

        return str_replace('http://', 'https://', $url);
    }

    public function testConnection(): array
    {
        $steps = [];
        $ok = true;

        $steps[] = '1. TCP...';
        try {
            $host = parse_url($this->base_url, PHP_URL_HOST);
            $port = parse_url($this->base_url, PHP_URL_PORT) ?: 443;
            $fp = @fsockopen($host, (int) $port, $errno, $errstr, 5);
            $steps[] = $fp ? '   ✓ Reachable' : "   ✗ {$errstr}";
            $fp && fclose($fp);
            $ok = $ok && (bool) $fp;
        } catch (\Throwable $e) {
            $steps[] = '   ✗ '.$e->getMessage();
            $ok = false;
        }

        if ($ok) {
            $steps[] = '2. HTTP...';
            try {
                $r = Http::timeout(5)->withOptions(['verify' => $this->ssl_verify])->get($this->api_base_url);
                $steps[] = '   ✓ HTTP '.$r->status();
            } catch (\Throwable $e) {
                $steps[] = '   ✗ '.$e->getMessage();
                $ok = false;
            }
        }

        if ($ok) {
            $steps[] = '3. Keycloak Auth...';
            try {
                $token = (new AuthService($this))->getToken();
                $steps[] = '   ✓ Token ('.mb_substr($token, 0, 20).'...)';
            } catch (\Throwable $e) {
                $steps[] = '   ✗ '.$e->getMessage();
                $ok = false;
            }
        }

        if ($ok) {
            $steps[] = '4. API...';
            try {
                $client = new Client($this);
                $client->get('studies', ['limit' => 1]);
                $steps[] = '   ✓ API OK';
            } catch (\Throwable $e) {
                $steps[] = '   ✗ '.$e->getMessage();
                $ok = false;
            }
        }

        return ['ok' => $ok, 'steps' => $steps];
    }
}
