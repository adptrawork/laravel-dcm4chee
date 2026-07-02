<?php

namespace App\Services\Dcm4chee;

use App\Models\Server;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class AuthService
{
    public function __construct(
        protected Server $server,
    ) {}

    public function getToken(): string
    {
        return Cache::remember(
            $this->cacheKey(),
            280,
            fn () => $this->fetchToken(),
        );
    }

    public function refreshToken(): string
    {
        Cache::forget($this->cacheKey());

        return $this->getToken();
    }

    protected function fetchToken(): string
    {
        $response = Http::timeout($this->server->timeout)
            ->withOptions(['verify' => $this->server->ssl_verify])
            ->asForm()
            ->post($this->server->keycloak_url . '/realms/dcm4che/protocol/openid-connect/token', [
                'client_id' => 'dcm4chee-arc-rs',
                'client_secret' => 'changeit',
                'grant_type' => 'password',
                'username' => $this->server->username,
                'password' => Crypt::decryptString($this->server->password),
            ]);

        $response->throw();

        return $response->json('access_token');
    }

    protected function cacheKey(): string
    {
        return "dcm4chee_token_{$this->server->id}";
    }
}
