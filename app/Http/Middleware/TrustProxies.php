<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Get the trusted proxies for the request.
     * Supports wildcard ('*') or comma-separated IP/CIDR addresses via config('trustedproxy.proxies').
     *
     * @return array<int, string>|string|null
     */
    protected function proxies(): array|string|null
    {
        $trusted = config('trustedproxy.proxies', $this->proxies);

        if ($trusted === '*' || $trusted === '**') {
            return '*';
        }

        if (is_string($trusted) && trim($trusted) !== '') {
            return array_map('trim', explode(',', $trusted));
        }

        return is_array($trusted) ? $trusted : $this->proxies;
    }
}
