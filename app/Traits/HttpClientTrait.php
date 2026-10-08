<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait HttpClientTrait
{
    function post(
        string $url,
        array $data = [],
        array $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
    ) {
        $response = Http::withHeaders($headers)
            ->asJson()
            ->post($url, $data);
        return $response;
    }

    function get(
        string $url,
        array $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ],
    ) {
        $response = Http::withHeaders($headers)->timeout(60 * 10)->get($url);
        return $response;
    }

    function remove(
        string $url,
        array $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
    ) {
        $response = Http::withHeaders($headers)->delete($url);
        return $response;
    }
}
