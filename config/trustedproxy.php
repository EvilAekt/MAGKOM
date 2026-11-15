<?php

declare(strict_types=1);

use Illuminate\Http\Request;

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | The IP addresses, CIDR blocks, or '*' for the trusted reverse proxies
    | and load balancers (Cloudflare, AWS ALB, Nginx, HAProxy).
    |
    | Values can be:
    | - '*' to trust all reverse proxies (e.g. dynamic load balancers)
    | - Comma-separated list of IP addresses or CIDR ranges (e.g. '10.0.0.0/8,172.16.0.0/12')
    | - null or empty string to disable proxy trusting (direct connection)
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),

    /*
    |--------------------------------------------------------------------------
    | Proxy Headers
    |--------------------------------------------------------------------------
    |
    | The HTTP headers that should be used to detect proxies and client IPs.
    |
    */

    'headers' => Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB,

];
