<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DispatchesApiRequests
{
    // Dispatch an internal API request through the router.
    // This keeps Livewire components on the API-driven path.
    protected function dispatchApiRequest(string $method, string $endpoint, array $data = [], array $query = []): object
    {
        $basePath = request()->getBasePath() ?: '';
        $uri = $basePath . '/api' . (str_starts_with($endpoint, '/') ? $endpoint : '/' . $endpoint);

        if (! empty($query)) {
            $uri .= '?' . http_build_query($query);
        }

        $server = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        if ($token = $this->resolveApiToken()) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        $content = in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            ? json_encode($data)
            : null;

        $parameters = [];

        $request = Request::create($uri, $method, $parameters, request()->cookies->all(), [], $server, $content);
        $response = app('router')->dispatch($request);

        $payload = null;
        $body = $response->getContent();
        if (is_string($body) && $body !== '') {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        return (object) [
            'ok' => $response->isSuccessful(),
            'status' => $response->getStatusCode(),
            'payload' => $payload,
            'response' => $response,
        ];
    }

    protected function resolveApiToken(): ?string
    {
        if ($token = request()->bearerToken()) {
            return $token;
        }

        if ($token = request()->cookie('api_token')) {
            return $token;
        }

        return null;
    }

    protected function apiGet(string $endpoint, array $query = []): object
    {
        return $this->dispatchApiRequest('GET', $endpoint, [], $query);
    }

    protected function apiPost(string $endpoint, array $data = []): object
    {
        return $this->dispatchApiRequest('POST', $endpoint, $data);
    }

    protected function apiPut(string $endpoint, array $data = []): object
    {
        return $this->dispatchApiRequest('PUT', $endpoint, $data);
    }

    protected function apiPatch(string $endpoint, array $data = []): object
    {
        return $this->dispatchApiRequest('PATCH', $endpoint, $data);
    }

    protected function apiDelete(string $endpoint, array $data = []): object
    {
        return $this->dispatchApiRequest('DELETE', $endpoint, $data);
    }
}
