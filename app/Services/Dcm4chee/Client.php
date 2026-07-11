<?php

namespace App\Services\Dcm4chee;

use App\Models\AuditLog;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Client
{
    protected AuthService $auth;

    public function __construct(
        protected Server $server,
    ) {
        $this->auth = new AuthService($server);
    }

    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->call('GET', $path, options: [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    public function post(string $path, mixed $body = null, array $headers = []): array
    {
        return $this->call('POST', $path, options: [
            'body' => $body,
            'headers' => $headers,
        ]);
    }

    public function put(string $path, mixed $body = null, array $headers = []): array
    {
        return $this->call('PUT', $path, options: [
            'body' => $body,
            'headers' => $headers,
        ]);
    }

    public function delete(string $path, array $headers = []): array
    {
        return $this->call('DELETE', $path, options: [
            'headers' => $headers,
        ]);
    }

    public function raw(string $method, string $path, array $options = [], ?string $prefix = null): \Illuminate\Http\Client\Response
    {
        $token = $this->auth->getToken();

        if (isset($options['body']) && is_array($options['body'])) {
            $options['body'] = json_encode($options['body']);
            $options['headers']['Content-Type'] ??= 'application/dicom+json';
        }

        $request = Http::timeout($this->server->timeout)
            ->withToken($token)
            ->withOptions([
                'verify' => $this->server->ssl_verify,
            ]);

        $base = $this->server->api_base_url;
        if ($prefix !== null) {
            $base = str_replace('/aets/DCM4CHEE/rs', '/aets/DCM4CHEE/' . $prefix, $base);
        }
        $url = $base . '/' . ltrim($path, '/');

        $start = microtime(true);
        $response = $request->send($method, $url, $options);
        $duration = (int) ((microtime(true) - $start) * 1000);

        $this->log($method, $path, $url, $options, $response, $duration);

        return $response;
    }

    protected function call(string $method, string $path, array $options = [], int $attempt = 1): array
    {
        $response = $this->raw($method, $path, $options);

        if ($response->status() === 401 && $attempt === 1) {
            $this->auth->refreshToken();

            return $this->call($method, $path, $options, attempt: 2);
        }

        $response->throw();

        return $response->json() ?? [];
    }

    protected function log(string $method, string $endpoint, string $url, array $options, $response, int $durationMs): void
    {
        $body = $options['body'] ?? null;

        AuditLog::create([
            'user_id' => Auth::id(),
            'server_id' => $this->server->id,
            'method' => $method,
            'endpoint' => $endpoint,
            'url' => $url,
            'request_body' => is_string($body) ? $body : json_encode($body),
            'response_body' => mb_substr($response->body(), 0, 65535),
            'response_status' => $response->status(),
            'duration_ms' => $durationMs,
            'status' => $response->successful() ? 'success' : 'error',
        ]);
    }
}
