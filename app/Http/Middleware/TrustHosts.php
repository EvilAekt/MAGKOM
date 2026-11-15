<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        $customHosts = (array) config('app.trusted_hosts', []);

        return array_values(array_filter(array_merge([
            $this->allSubdomainsOfApplicationUrl(),
            'localhost',
            '127.0.0.1',
        ], $customHosts)));
    }
}
